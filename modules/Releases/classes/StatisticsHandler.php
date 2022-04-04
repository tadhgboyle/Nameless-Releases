<?php

class StatisticsHandler
{

    public static function handleRequest(array $data): void {
        try {
            $mysqli = new mysqli(Config::get('stats_credentials'));
        } catch (Exception $e) {
            return;
        }

        if ($mysqli->connect_errno) {
            return;
        }

        if (!isset($data['uid']) || !isset($data['version'])) {
            return;
        }

        if (!ctype_alnum($data['uid'])) {
            return;
        }

        if (!ctype_alnum(str_replace(array(".", "-"), "", $data['version']))) {
            return;
        }

        $uid = $mysqli->real_escape_string($data['uid']);

        if (strlen($uid) !== 62) {
            return;
        }

        if ($data['version'] == 'STAT-BREAK') {
            return;
        }

        $php_version = null;
        if (isset($data['php_version'])) {
            $php_version = explode('.', $data['php_version']);
            $php_version = count($php_version) >= 2 ? ($php_version[0] . '.' . $php_version[1]) : $php_version[0];
            $php_version = $mysqli->real_escape_string($php_version);
        }

        $version = $mysqli->real_escape_string($data['version']);

        $stmt = $mysqli->prepare("SELECT uniqueid FROM nl2_nl_stats WHERE uniqueid = ?");
        $stmt->bind_param("s", $data['uid']);
        $stmt->execute();
        $stmt->store_result();

        $date = date('U');

        if ($stmt->num_rows > 0) {
            $stmt_update = $mysqli->prepare("UPDATE nl2_nl_stats SET `version` = ?, `last_queried` = ?, `php_version` = ? WHERE `uniqueid` = ?");

            if (!$stmt_update) {
                return;
            }

            $stmt_update->bind_param("siss", $version, $date, $php_version, $uid);

            if (!$stmt_update->execute()) {
                return;
            }

            $stmt_update->close();
        } else {
            $stmt_insert = $mysqli->prepare("INSERT INTO nl2_nl_stats (`uniqueid`, `version`, `date`, `last_queried`, `php_version`) VALUES (?, ?, ?, ?, ?)");

            if (!$stmt_insert) {
                return;
            }

            $stmt_insert->bind_param("ssiis", $uid, $version, $date, $date, $php_version);

            if (!$stmt_insert->execute()) {
                return;
            }

            $stmt_insert->close();
        }

        $stmt->free_result();
        $stmt->close();
        $mysqli->close();
    }
}
