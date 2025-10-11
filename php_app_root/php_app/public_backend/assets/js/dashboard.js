/**
 * Dashboard Page Specific JavaScript
 * Dashboard-only functionality for b-dashboard page
 */

// ========== DASHBOARD VARIABLES ========== 
let chart;
let chartData;
let currentDateSelection = null;
let currentChartData = null;

// ========== DASHBOARD INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    setupDateRangeFunctionality();
    initChart();
    AdminCommon.setupNotificationBlink();

    //date range pick DD
    window.AdminCommon.setupDropdown('dateRangeBtn', 'dateRangeDropdown');

    // 默认加载最近30天数据
    loadChartDataFromDropdown(30);
}

// ========== DATE RANGE FUNCTIONALITY ========== 
function setupDateRangeFunctionality() {
    setupDateRangeDropdown();
    setupCustomDateRange();
}

function setupDateRangeDropdown() {
    // Setup date range dropdown if not already set up in main.js
    if (!document.getElementById('dateRangeDropdown')) return;

    // Quick date range options
    document.querySelectorAll('.date-range-option').forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;
            const text = option.textContent;

            // Update active state
            document.querySelectorAll('.date-range-option').forEach(opt => {
                opt.classList.remove('active');
            });
            option.classList.add('active');

            // Update button text
            const dateRangeText = document.getElementById('dateRangeText');
            if (dateRangeText) dateRangeText.textContent = text;

            // Close dropdown
            document.getElementById('dateRangeDropdown')?.classList.remove('show');

            // Mark as last selection method
            currentDateSelection = 'dropdown';

            // Load chart data from API
            loadChartDataFromDropdown(value);
        });
    });
}

function setupCustomDateRange() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    if (!startDateInput || !endDateInput) return;

    // Set default dates (last 30 days)
    function setDefaultDates() {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 30);

        endDateInput.value = endDate.toISOString().split('T')[0];
        startDateInput.value = startDate.toISOString().split('T')[0];
    }

    setDefaultDates();

    // Handle custom date range changes
    function handleCustomDateChange() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate && startDate <= endDate) {
            // Mark as last selection method
            currentDateSelection = 'custom';

            // Clear dropdown selection
            document.querySelectorAll('.date-range-option').forEach(opt => {
                opt.classList.remove('active');
            });
            const dateRangeText = document.getElementById('dateRangeText');
            if (dateRangeText) dateRangeText.textContent = '自定义范围';

            // Load chart data from API
            loadChartDataFromCustomRange(startDate, endDate);
        }
    }

    startDateInput.addEventListener('change', handleCustomDateChange);
    endDateInput.addEventListener('change', handleCustomDateChange);
}

// ========== CHART FUNCTIONALITY ==========
function initChart() {
    // Check if Chart is available
    if (typeof Chart === 'undefined') {
        console.log('Chart.js not yet loaded, retrying...');
        setTimeout(initChart, 100);
        return;
    }

    // Update chart when theme changes
    setupChartThemeObserver();
}

/**
 * 从下拉选项加载图表数据
 */
function loadChartDataFromDropdown(value) {
    let days;

    switch(value) {
        case 'week':
            days = 7;
            break;
        case '90':
            days = 90;
            break;
        case '180':
            days = 180;
            break;
        default:
            days = parseInt(value);
    }

    // 计算日期范围
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days + 1);

    const startDateStr = startDate.toISOString().split('T')[0];
    const endDateStr = endDate.toISOString().split('T')[0];

    // 同步更新自定义日期范围输入框
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    if (startDateInput && endDateInput) {
        startDateInput.value = startDateStr;
        endDateInput.value = endDateStr;
    }

    // 调用API获取数据
    fetchChartData(startDateStr, endDateStr);
}

/**
 * 从自定义日期范围加载图表数据
 */
function loadChartDataFromCustomRange(startDate, endDate) {
    fetchChartData(startDate, endDate);
}

/**
 * 从API获取图表数据
 */
function fetchChartData(startDate, endDate) {
    const url = `/dashboard/chart-data?start_date=${startDate}&end_date=${endDate}&precision=day`;

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                currentChartData = result.data;
                createChart(result.data);
            } else {
                console.error('Failed to load chart data:', result.message);
                showToast('加载图表数据失败: ' + result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
            showToast('获取图表数据时发生错误', 'error');
        });
}

function createChart(data) {
    if (chart) chart.destroy();

    const ctx = document.getElementById('analyticsChart')?.getContext('2d');
    if (!ctx) return;

    const labels = data.map(d => {
        const date = new Date(d.date);
        return `${date.getMonth() + 1}/${date.getDate()}`;
    });

    // Get current theme for chart colors
    const html = document.documentElement;
    const currentDataTheme = html.getAttribute('data-theme');
    const isDark = currentDataTheme === 'dark';

    const textColor = isDark ? '#cbd5e1' : '#64748b';
    const gridColor = isDark ? '#334155' : '#e2e8f0';
    const backgroundColor = isDark ? '#1e293b' : '#ffffff';

    // 创建混合图表：柱状图(新增、发布) + 折线图(总数、播放、用户)
    chart = new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [
                // 折线图 - 当日总视频数量
                {
                    type: 'line',
                    label: '当日总视频数量',
                    data: data.map(d => d.total_videos),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: false,
                    yAxisID: 'y'
                },
                // 柱状图 - 当日新增视频数量
                {
                    type: 'bar',
                    label: '当日新增视频数量',
                    data: data.map(d => d.new_videos),
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                // 柱状图 - 当日发布视频数量
                {
                    type: 'bar',
                    label: '当日发布视频数量',
                    data: data.map(d => d.published_videos),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                // 折线图 - 当日播放视频数量 (TODO: 暂无数据)
                {
                    type: 'line',
                    label: '当日播放视频数量',
                    data: data.map(d => d.video_plays || 0),
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ec4899',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: false,
                    yAxisID: 'y',
                    hidden: true // 默认隐藏，因为暂无数据
                },
                // 折线图 - 当日网站注册人数 (TODO: 暂无数据)
                {
                    type: 'line',
                    label: '当日网站注册人数',
                    data: data.map(d => d.new_users || 0),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: false,
                    yAxisID: 'y',
                    hidden: true // 默认隐藏，因为暂无数据
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        color: textColor,
                        padding: 15,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: backgroundColor,
                    titleColor: textColor,
                    bodyColor: textColor,
                    borderColor: gridColor,
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    grid: {
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            size: 10
                        },
                        callback: function(value) {
                            if (value >= 1000) {
                                return (value / 1000).toFixed(1) + 'K';
                            }
                            return value;
                        }
                    }
                }
            }
        }
    });

    currentChartData = data;
}


function setupChartThemeObserver() {
    const html = document.documentElement;
    
    // Update chart when theme changes
    function updateChartTheme() {
        if (chart && currentChartData) {
            createChart(currentChartData);
        }
    }
    
    // Add theme change observer for chart colors
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                updateChartTheme();
            }
        });
    });
    
    observer.observe(html, {
        attributes: true,
        attributeFilter: ['data-theme']
    });
}