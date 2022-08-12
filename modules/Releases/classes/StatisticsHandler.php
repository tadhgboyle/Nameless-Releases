<?php

class StatisticsHandler
{

    public static function handleRequest(array $data): void {
        try {
            $db = DB::getCustomInstance(
                Config::get('stats_credentials.host'),
                Config::get('stats_credentials.db'),
                Config::get('stats_credentials.username'),
                Config::get('stats_credentials.password')
            );
        } catch (Exception $e) {
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

        if (strlen($data['uid']) !== 62) {
            return;
        }

        if ($data['version'] == 'STAT-BREAK') {
            return;
        }

        $php_version = null;
        if (isset($data['php_version'])) {
            $php_version = explode('.', $data['php_version']);
            $php_version = count($php_version) >= 2 ? ($php_version[0] . '.' . $php_version[1]) : $php_version[0];
        }

        $date = date('U');

        if ($db->query('SELECT COUNT(*) AS total FROM nl2_nl_stats WHERE `uniqueid` = ?', [$data['uid']])->first()->total) {
            $db->query('UPDATE nl2_nl_stats SET `version` = ?, `last_queried` = ?, `php_version` = ? WHERE `uniqueid` = ?', [
                $data['version'],
                $date,
                $php_version,
                $data['uid'],
            ]);
        } else {
            $db->query('INSERT INTO nl2_nl_stats (`uniqueid`, `version`, `date`, `last_queried`, `php_version`) VALUES (?, ?, ?, ?, ?)', [
                $data['uid'],
                $data['version'],
                $date,
                $date,
                $php_version,
            ]);
        }
    }
}
