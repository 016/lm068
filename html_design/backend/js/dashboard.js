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
            
            // Update chart
            updateChartFromDropdown(value);
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
            
            // Update chart
            updateChartFromCustomRange(startDate, endDate);
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

    const ctx = document.getElementById('analyticsChart')?.getContext('2d');
    if (!ctx) return;
    
    // Generate initial chart data
    chartData = generateData(30);
    currentChartData = chartData;
    
    // Create initial chart
    createChart(chartData);
    
    // Make functions globally accessible for date range functionality
    window.generateData = generateData;
    window.generateCustomRangeData = generateCustomRangeData;
    window.createChart = createChart;
    
    // Update chart when theme changes
    setupChartThemeObserver();
}

function generateData(days) {
    const data = [];
    const baseDate = new Date();
    baseDate.setDate(baseDate.getDate() - days);
    
    for (let i = 0; i <= days; i++) {
        const date = new Date(baseDate);
        date.setDate(date.getDate() + i);
        data.push({
            date: date.toISOString().split('T')[0],
            totalVideos: Math.floor(Math.random() * 50) + 100,      // 总视频数量
            videoViews: Math.floor(Math.random() * 1000) + 2000,    // 视频播放数量
            totalUsers: Math.floor(Math.random() * 200) + 300       // 网站总注册人数
        });
    }
    return data;
}

function generateCustomRangeData(startDate, endDate, days) {
    const data = [];
    const currentDate = new Date(startDate);
    
    for (let i = 0; i < days; i++) {
        data.push({
            date: currentDate.toISOString().split('T')[0],
            totalVideos: Math.floor(Math.random() * 50) + 100,
            videoViews: Math.floor(Math.random() * 1000) + 2000,
            totalUsers: Math.floor(Math.random() * 200) + 300
        });
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return data;
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
    
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '总视频数量',
                    data: data.map(d => d.totalVideos),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: true
                },
                {
                    label: '视频播放数量',
                    data: data.map(d => d.videoViews),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: true
                },
                {
                    label: '网站总注册人数',
                    data: data.map(d => d.totalUsers),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: true
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
                        padding: 20,
                        font: {
                            size: 12,
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
                    callbacks: {
                        afterLabel: function(context) {
                            if (context.datasetIndex === 1) {
                                return '播放量';
                            } else if (context.datasetIndex === 2) {
                                return '注册用户';
                            }
                            return '视频数';
                        }
                    }
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
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            size: 11
                        },
                        callback: function(value, index, values) {
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

function updateChartFromDropdown(value) {
    let days;
    
    switch(value) {
        case 'week':
            // Current week (Monday to Sunday)
            const today = new Date();
            const currentDay = today.getDay();
            const mondayOffset = currentDay === 0 ? -6 : 1 - currentDay;
            days = 7;
            break;
        case '90':
            days = 90; // Quarter
            break;
        default:
            days = parseInt(value);
    }
    
    const chartData = generateData(days);
    createChart(chartData);
}

function updateChartFromCustomRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const daysDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    
    const chartData = generateCustomRangeData(start, end, daysDiff);
    createChart(chartData);
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