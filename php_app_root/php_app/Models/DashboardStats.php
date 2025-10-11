<?php

namespace App\Models;

/**
 * DashboardStats Model
 */
class DashboardStats
{
    /**
     *
     * @return array
     */
    public static function getMetricsOverview(): array
    {
        $contentStats = Content::getTotalAndMonthlyStats();

        $userStats = User::getTotalAndMonthlyStats();

        $subscriptionStats = Subscription::getActiveSubscriberStats();

        return [
            'total_videos' => $contentStats['total'],
            'monthly_new_videos' => $contentStats['monthly_new'],
            'monthly_growth_rate' => $contentStats['monthly_growth_rate'],

            'total_views' => 0,

            'total_users' => $userStats['total'],
            'monthly_new_users' => $userStats['monthly_new'],
            'user_monthly_growth_rate' => $userStats['monthly_growth_rate'],

            'total_subscribers' => $subscriptionStats['total'],
            'monthly_new_subscribers' => $subscriptionStats['monthly_new'],
            'subscriber_monthly_growth_rate' => $subscriptionStats['monthly_growth_rate']
        ];
    }

    /**
     * ��content-grid:Wpn
     * tContent, Comment�ߡpn
     *
     * @return array
     */
    public static function getContentGridStats(): array
    {
        $videoStats = Content::getStatusStats();

        $commentStats = Comment::getStatusStats();

        return [
            'video_stats' => $videoStats,
            'comment_stats' => $commentStats,
            'queue_stats' => [
                'new' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'failed' => 0
            ]
        ];
    }

    /**
     *
     * @param string $startDate (Y-m-d)
     * @param string $endDate  (Y-m-d)
     * @param string $precision (day, hour)
     * @return array
     */
    public static function getChartData(string $startDate, string $endDate, string $precision = 'day'): array
    {
        if ($precision === 'hour') {
            return [];
        }

        $contentDailyStats = Content::getDailyStats($startDate, $endDate);

        foreach ($contentDailyStats as &$dayStat) {
            $dayStat['video_plays'] = 0;
            $dayStat['new_users'] = 0;
        }

        return $contentDailyStats;
    }
}
