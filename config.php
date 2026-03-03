<?php
// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Database Configuration â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change if you set a password
define('DB_PASS', '');           // Default XAMPP has no password
define('DB_NAME', 'acadmate_db');

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ CORS Headers (allow Android app) â”€â”€â”€â”€â”€
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Create PDO connection â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function getDB(): PDO {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ JSON response helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function success($data = [], string $message = 'OK'): void {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit();
}

function error(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message, 'data' => null]);
    exit();
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Auth token validation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function requireAuth(PDO $db): array {
    $headers = getallheaders();
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (empty($auth) || !str_starts_with($auth, 'Bearer ')) {
        error('Unauthorized: missing token', 401);
    }
    $token = trim(substr($auth, 7));
    $stmt  = $db->prepare('SELECT id, full_name, email, course FROM users WHERE auth_token = ?');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if (!$user) {
        error('Unauthorized: invalid or expired token', 401);
    }
    return $user;
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Request body helper â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Expected classes calculator â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Returns an associative array: [subject_id => expected_class_count]
// Expected = number of times the slot's weekday has occurred from
// semester start_date (or slot creation date if no semester) up to today.
function getExpectedClasses(PDO $db, int $userId, int $semesterId = 0, string $deviceHHMM = ''): array {
    $semFilter = $semesterId > 0 ? 'AND sub.semester_id = :semId' : '';
    $stmt = $db->prepare("
        SELECT cs.id AS slot_id, cs.subject_id, cs.day AS slot_day,
               cs.end_time,
               sem.start_date
          FROM class_slots cs
          JOIN subjects sub ON sub.id = cs.subject_id
          JOIN semesters sem ON sem.id = sub.semester_id
         WHERE cs.user_id = :uid $semFilter
    ");
    $params = [':uid' => $userId];
    if ($semesterId > 0) $params[':semId'] = $semesterId;
    $stmt->execute($params);
    $slots = $stmt->fetchAll();

    $dayMap = [
        'Monday'    => 1, 'Tuesday'  => 2, 'Wednesday' => 3,
        'Thursday'  => 4, 'Friday'   => 5, 'Saturday'  => 6, 'Sunday' => 0
    ];

    // subject_id => count of expected (sum over all its slots)
    $expected = [];
    $today = new DateTime('today'); // today at midnight (server date)
    // Use device-provided HH:MM to get accurate "current time" regardless of
    // server timezone (server may be UTC while device is IST or another zone).
    if (!empty($deviceHHMM) && preg_match('/^\d{1,2}:\d{2}$/', trim($deviceHHMM))) {
        $parts = explode(':', trim($deviceHHMM));
        $now = clone $today;
        $now->setTime((int)$parts[0], (int)$parts[1], 0);
    } else {
        $now = new DateTime(); // fallback: server time
    }

    foreach ($slots as $slot) {
        $subId   = (int) $slot['subject_id'];
        $dayName = $slot['slot_day'];
        $startDate = new DateTime($slot['start_date']);

        if ($startDate > $today) {
            // Semester hasn't started yet
            $expected[$subId] = ($expected[$subId] ?? 0) + 0;
            continue;
        }

        $targetDow = $dayMap[$dayName] ?? -1;
        if ($targetDow === -1) { continue; }

        // Real-time cutoff: only count today if this slot's class has already ended.
        // If today is this slot's weekday but the end_time hasn't passed yet,
        // the class is either in progress or hasn't started â€” exclude today.
        $todayDow = (int) $today->format('w'); // 0=Sun
        $cutoff   = clone $today;
        if ($todayDow === $targetDow && !empty($slot['end_time'])) {
            $parts = explode(':', $slot['end_time']);
            $slotEndToday = clone $today;
            $slotEndToday->setTime((int)$parts[0], (int)($parts[1] ?? 0), 0);
            if ($now < $slotEndToday) {
                // Class hasn't ended yet â€” don't count today
                $cutoff->modify('-1 day');
            }
        }

        if ($startDate > $cutoff) {
            $expected[$subId] = ($expected[$subId] ?? 0) + 0;
            continue;
        }

        // Count occurrences of weekday from startDate to cutoff (inclusive)
        $diff  = (int) $startDate->diff($cutoff)->days;
        $count = 0;
        for ($d = 0; $d <= $diff; $d++) {
            $ts = clone $startDate;
            $ts->modify("+$d day");
            if ((int) $ts->format('w') === $targetDow) {
                $count++;
            }
        }
        $expected[$subId] = ($expected[$subId] ?? 0) + $count;
    }

    return $expected;
}
