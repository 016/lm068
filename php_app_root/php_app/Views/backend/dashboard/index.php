<!-- Dashboard Content -->
<main class="dashboard-content">
    <!-- Key Metrics Overview -->
    <div class="metrics-overview">
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon videos">
                    <i class="bi bi-camera-video" style="color: white; font-size: 20px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>总视频数</h4>
                    <span class="metric-subtitle">数据库中视频总数</span>
                </div>
            </div>
            <div class="metric-card-body">
                <div class="metric-value"><?= number_format($metrics['total_videos']) ?></div>
                <div class="metric-change <?= $metrics['monthly_growth_rate'] > 0 ? 'positive' : '' ?>">
                    <i class="bi bi-trending-up" style="font-size: 16px;"></i>
                    +<?= $metrics['monthly_growth_rate'] ?>% 本月
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon views">
                    <i class="bi bi-eye" style="color: white; font-size: 20px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>总观看次数</h4>
                    <span class="metric-subtitle">全平台播放量统计</span>
                </div>
            </div>
            <div class="metric-card-body">
                <!-- TODO: 暂无数据，使用0占位，待后续实现 -->
                <div class="metric-value"><?= number_format($metrics['total_views']) ?></div>
                <div class="metric-change">
                    <i class="bi bi-dash" style="font-size: 16px;"></i>
                    暂无数据
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon users">
                    <i class="bi bi-people" style="color: white; font-size: 20px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>注册用户</h4>
                    <span class="metric-subtitle">活跃用户账户数</span>
                </div>
            </div>
            <div class="metric-card-body">
                <div class="metric-value"><?= number_format($metrics['total_users']) ?></div>
                <div class="metric-change <?= $metrics['user_monthly_growth_rate'] > 0 ? 'positive' : '' ?>">
                    <i class="bi bi-trending-up" style="font-size: 16px;"></i>
                    +<?= $metrics['user_monthly_growth_rate'] ?>% 本月
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon subscribers">
                    <i class="bi bi-envelope" style="color: white; font-size: 20px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>邮件订阅者</h4>
                    <span class="metric-subtitle">活跃订阅用户数</span>
                </div>
            </div>
            <div class="metric-card-body">
                <div class="metric-value"><?= number_format($metrics['total_subscribers']) ?></div>
                <div class="metric-change <?= $metrics['subscriber_monthly_growth_rate'] > 0 ? 'positive' : '' ?>">
                    <i class="bi bi-trending-up" style="font-size: 16px;"></i>
                    +<?= $metrics['subscriber_monthly_growth_rate'] ?>% 本月
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards Grid - Updated to match metric card design -->
    <div class="content-grid">
        <div class="metric-card-updated">
            <div class="metric-card-header">
                <div class="metric-icon-small videos">
                    <i class="bi bi-camera-video" style="color: white; font-size: 16px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>视频状态统计</h4>
                    <span class="metric-subtitle">内容管理概览</span>
                </div>
            </div>
            <div class="metric-card-body">
                <div class="status-grid-updated">
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['content_stats']['published']) ?></div>
                        <div class="status-label-updated">已发布</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['content_stats']['pending_publish']) ?></div>
                        <div class="status-label-updated">待发布</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['content_stats']['shooting_done']) ?></div>
                        <div class="status-label-updated">拍摄完</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['content_stats']['script_done']) ?></div>
                        <div class="status-label-updated">脚本完</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card-updated">
            <div class="metric-card-header">
                <div class="metric-icon-small security">
                    <i class="bi bi-shield-check" style="color: white; font-size: 16px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>评论统计</h4>
                    <span class="metric-subtitle">评论状态统计</span>
                </div>
            </div>
            <div class="metric-card-body">
                <!-- TODO: 暂无数据，使用0占位，待后续实现 -->
                <div class="status-grid-updated">
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['comment_stats']['total']) ?></div>
                        <div class="status-label-updated">总数</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['comment_stats']['pending']) ?></div>
                        <div class="status-label-updated">待审核</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['comment_stats']['approved']) ?></div>
                        <div class="status-label-updated">审核通过</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['comment_stats']['hidden']) ?></div>
                        <div class="status-label-updated">已隐藏</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card-updated">
            <div class="metric-card-header">
                <div class="metric-icon-small moderation">
                    <i class="bi bi-hammer" style="color: white; font-size: 16px;"></i>
                </div>
                <div class="metric-header-text">
                    <h4>数据处理队列</h4>
                    <span class="metric-subtitle">抓取数据任务</span>
                </div>
            </div>
            <div class="metric-card-body">
                <!-- TODO: 暂无数据，使用0占位，待后续实现 -->
                <div class="status-grid-updated">
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['queue_stats']['new']) ?></div>
                        <div class="status-label-updated">新任务</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['queue_stats']['in_progress']) ?></div>
                        <div class="status-label-updated">进行中</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['queue_stats']['completed']) ?></div>
                        <div class="status-label-updated">已完成</div>
                    </div>
                    <div class="status-item-updated">
                        <div class="status-number-updated"><?= number_format($contentGrid['queue_stats']['failed']) ?></div>
                        <div class="status-label-updated">失败</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Chart with Chart.js - ENHANCED with Custom Date Range -->
    <div class="chart-container">
        <div class="chart-header">
            <div class="chart-header-left">
                <i class="bi bi-trending-up card-icon" style="font-size: 20px;"></i>
                <h3>数据趋势分析</h3>
            </div>
            <div class="chart-controls">
                <!-- 快捷时间选择 -->
                <div class="dropdown-container">
                    <button class="date-range-btn" id="dateRangeBtn">
                        <span id="dateRangeText">最近30天</span>
                        <i class="bi bi-chevron-down" style="font-size: 16px;"></i>
                    </button>
                    <div class="dropdown-menu" id="dateRangeDropdown">
                        <div class="dropdown-body">
                            <div class="date-range-option" data-value="7">最近7天</div>
                            <div class="date-range-option" data-value="10">最近10天</div>
                            <div class="date-range-option" data-value="15">最近15天</div>
                            <div class="date-range-option active" data-value="30">最近30天</div>
                            <div class="date-range-option" data-value="week">本周</div>
                            <div class="date-range-option" data-value="90">本季度</div>
                            <div class="date-range-option" data-value="180">最近6个月</div>
                        </div>
                    </div>
                </div>
                
                <!-- 自定义时间范围选择器 -->
                <div class="custom-date-range">
                    <input type="date" id="startDate" />
                    <span class="date-separator">至</span>
                    <input type="date" id="endDate" />
                </div>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="analyticsChart" width="400" height="200"></canvas>
        </div>
    </div>
</main>