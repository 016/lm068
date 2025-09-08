/**
 * Tag View Page Specific JavaScript
 * 标注：从 tag_view_2.html 抽离的页面专用JavaScript
 */

// ========== TAG VIEW PAGE INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeTagViewPage();
});

function initializeTagViewPage() {
    console.log('Initializing Tag View page...');
    
    // Initialize common admin functionality
    if (window.AdminCommon && window.AdminCommon.TagViewUtils) {
        window.AdminCommon.TagViewUtils.initializeTagViewEffects();
        window.AdminCommon.TagViewUtils.initializeAnimatedCounters();
    }
    
    // Page-specific initialization
    setupTagViewInteractions();
    
    console.log('Tag View page initialization completed');
}

// ========== TAG VIEW SPECIFIC INTERACTIONS ==========
function setupTagViewInteractions() {
    // No specific interactions needed for this page
    // All interactions are handled by the common admin functionality
    console.log('Tag View interactions setup completed');
}