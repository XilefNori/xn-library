<?php

namespace XnLibrary;

use App\Exception\AssertException;

class Datetime
{
    public const INTERVAL_DAY   = 1;
    public const INTERVAL_MONTH = 2;
    public const INTERVAL_YEAR  = 3;

    public const INTERVAL_NAME = [
        self::INTERVAL_DAY   => 'day',
        self::INTERVAL_MONTH => 'month',
        self::INTERVAL_YEAR  => 'year',
    ];

    public const FORMATS = [
        'sql' => [
            '.' => [
                self::INTERVAL_YEAR  => '%Y',
                self::INTERVAL_MONTH => '%m.%Y',
                self::INTERVAL_DAY   => '%d.%m.%Y',
            ],
            '-' => [
                self::INTERVAL_YEAR  => '%Y',
                self::INTERVAL_MONTH => '%Y-%m',
                self::INTERVAL_DAY   => '%Y-%m-%d',
            ]
        ],

        'php' => [
            '.' => [
                self::INTERVAL_YEAR  => 'Y',
                self::INTERVAL_MONTH => 'm.Y',
                self::INTERVAL_DAY   => 'd.m.Y',
            ],
            '-' => [
                self::INTERVAL_YEAR  => 'Y',
                self::INTERVAL_MONTH => 'Y-m',
                self::INTERVAL_DAY   => 'Y-m-d',
            ]
        ],

    ];

    /**
     * @param int    $interval
     * @param string $type (.|-) точка или тире
     *
     * @return string
     */
    public static function getFormatSql($interval, $type)
    {
        return self::FORMATS['sql'][$type][$interval];
    }

    /**
     * @param int    $interval
     * @param string $type
     *
     * @return string
     */
    public static function getFormatPhp($interval, $type)
    {
        return self::FORMATS['php'][$type][$interval];
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public static function now()
    {
        return new \DateTime();
    }

    /**
     * @param \DateTime|string|int $moment
     *
     * @return \DateTime
     * @throws AssertException
     * @throws \Exception
     * @internal param string $format
     */
    public static function getDT($moment): \DateTime
    {
        if (!$moment) {
            throw new AssertException('You must set moment!');
        }

        if ($moment instanceof \DateTime) {
            $dt = clone $moment;
        } else if (is_string($moment)) {
            if (!is_numeric($moment)) {
                $dt = new \DateTime($moment);
            } else {
                $dt = new \DateTime();

                $dt->setTimestamp($moment);
            }
        } else if (is_int($moment)) {
            $dt = new \DateTime();

            $dt->setTimestamp($moment);
        } else {
            throw new AssertException('Unknown type of moment: ' . var_export($moment, 1));
        }

        return $dt;
    }

    /**
     * @param \DateTime|string|int $time
     * @param int                  $date_only
     *
     * @return string
     * @throws AssertException
     */
    public static function str($time, $date_only = 0)
    {
        $format_dt = 'Y-m-d H:i:s';
        if ($date_only) {
            $format_dt = 2 == (int) $date_only ? 'Y-m-d' : 'Y-m-d 00:00:00';
        }

        return self::getDT($time)->format($format_dt);
    }

    /**
     * @param \DateInterval|string $interval
     * @param bool                 $backward
     * @param \DateTime            $moment
     *
     * @return \DateTime
     * @throws \Exception
     */
    public static function getDTbyInterval($interval, $backward, $moment = null): \DateTime
    {
        if (!$moment) {
            $moment = new \DateTime();
        } else {
            $moment = clone $moment;
        }

        if (is_string($interval)) {
            $interval = new \DateInterval($interval);
        }

        if ($backward) {
            $moment->sub($interval);
        } else {
            $moment->add($interval);
        }

        return $moment;
    }
}
