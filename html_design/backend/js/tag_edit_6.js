/**
 * Tag Edit Page Specific JavaScript
 * Generated for tag_edit_form_5.html page
 */

// ========== PAGE-SPECIFIC INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeTagEditPage();
});

function initializeTagEditPage() {
    console.log('Initializing tag edit page...');
    
    // Initialize common switch functionality from main.js
    if (window.AdminCommon && window.AdminCommon.SwitchUtils) {
        window.AdminCommon.SwitchUtils.initializeSwitches();
        
        // Setup interactions for all switches
        setupTagEditSwitches();
    }
    
    // Initialize color picker sync
    setupColorPickerSync();
    
    // Initialize form submission
    setupTagEditForm();
    
    // Initialize tag preview
    setupTagPreview();
    
    console.log('Tag edit page initialization completed');
}

// ========== SWITCH FUNCTIONALITY ==========
function setupTagEditSwitches() {
    const switchIds = ['tagStatus', 'tagFeatured', 'tagDisabled'];
    
    switchIds.forEach(switchId => {
        const element = document.getElementById(switchId);
        if (element && window.AdminCommon.SwitchUtils) {
            window.AdminCommon.SwitchUtils.setupSwitchInteraction(switchId);
        }
    });
    
    console.log('Tag edit switches setup completed');
}

// ========== COLOR PICKER FUNCTIONALITY ==========
function setupColorPickerSync() {
    const colorInput = document.getElementById('tagColor');
    const colorHexInput = document.getElementById('tagColorHex');
    
    if (!colorInput || !colorHexInput) return;
    
    // Sync color picker to hex input
    colorInput.addEventListener('input', (e) => {
        colorHexInput.value = e.target.value;
        updateTagPreview();
    });
    
    // Sync hex input to color picker
    colorHexInput.addEventListener('input', (e) => {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            colorInput.value = e.target.value;
            updateTagPreview();
        }
    });
    
    // Validate hex input
    colorHexInput.addEventListener('blur', (e) => {
        if (!e.target.value.match(/^#[0-9A-F]{6}$/i)) {
            e.target.value = colorInput.value;
        }
    });
    
    console.log('Color picker sync setup completed');
}

// ========== TAG PREVIEW FUNCTIONALITY ==========
function setupTagPreview() {
    const tagNameInput = document.getElementById('tagName');
    
    if (tagNameInput) {
        tagNameInput.addEventListener('input', updateTagPreview);
        // Initial preview update
        updateTagPreview();
    }
}

function updateTagPreview() {
    const tagName = document.getElementById('tagName')?.value || '标签预览';
    const tagColor = document.getElementById('tagColor')?.value || '#6366f1';
    
    // Create or update preview element
    let previewContainer = document.getElementById('tagPreviewContainer');
    if (!previewContainer) {
        previewContainer = document.createElement('div');
        previewContainer.id = 'tagPreviewContainer';
        previewContainer.className = 'form-text';
        
        const tagNameGroup = document.getElementById('tagName')?.closest('.form-group');
        if (tagNameGroup) {
            tagNameGroup.appendChild(previewContainer);
        }
    }
    
    previewContainer.innerHTML = `
        <div class="tag-preview" style="background-color: ${tagColor}; color: white;">
            <i class="bi bi-tag tag-preview-icon"></i>
            ${tagName}
        </div>
    `;
}

// ========== FORM VALIDATION ==========
function validateTagEditForm() {
    const tagName = document.getElementById('tagName');
    const tagSlug = document.getElementById('tagSlug');
    let isValid = true;
    
    // Clear previous validation
    clearValidationErrors();
    
    // Validate tag name
    if (!tagName.value.trim()) {
        showValidationError(tagName, '标签名称不能为空');
        isValid = false;
    }
    
    // Validate slug format
    if (tagSlug.value && !/^[a-z0-9-]+$/.test(tagSlug.value)) {
        showValidationError(tagSlug, 'URL标识只能包含小写字母、数字和短横线');
        isValid = false;
    }
    
    return isValid;
}

function showValidationError(field, message) {
    field.classList.add('error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-error';
    errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
    
    field.parentNode.appendChild(errorDiv);
}

function clearValidationErrors() {
    // Remove error classes
    document.querySelectorAll('.form-control.error').forEach(field => {
        field.classList.remove('error');
    });
    
    // Remove error messages
    document.querySelectorAll('.validation-error').forEach(error => {
        error.remove();
    });
}

// ========== FORM SUBMISSION ==========
function setupTagEditForm() {
    const form = document.getElementById('tagEditForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateTagEditForm()) {
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 保存中...';
        
        try {
            // Collect form data
            const formData = new FormData(form);
            
            // Ensure unchecked checkboxes are handled properly
            handleUncheckedSwitches(formData);
            
            // Log form data for debugging
            console.log('Submitting form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Simulate API call (replace with actual endpoint)
            const response = await submitTagForm(formData);
            
            if (response.success) {
                if (window.AdminCommon.SwitchUtils) {
                    window.AdminCommon.SwitchUtils.showAlert('success', '标签信息已成功保存！');
                }
                
                // Optionally redirect or refresh data
                // window.location.href = '/admin/tags';
            } else {
                throw new Error(response.message || '保存失败');
            }
            
        } catch (error) {
            console.error('Form submission error:', error);
            
            if (window.AdminCommon.SwitchUtils) {
                window.AdminCommon.SwitchUtils.showAlert('danger', `保存失败: ${error.message}`);
            }
            
        } finally {
            // Restore submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalText;
        }
    });
    
    console.log('Tag edit form submission handler setup completed');
}

function handleUncheckedSwitches(formData) {
    const switches = [
        { id: 'tagStatus', name: 'status' },
        { id: 'tagFeatured', name: 'featured' },
        { id: 'tagDisabled', name: 'admin_locked' }
    ];
    
    switches.forEach(switchInfo => {
        const checkbox = document.getElementById(switchInfo.id);
        if (checkbox) {
            if (checkbox.checked) {
                formData.set(switchInfo.name, '1');
            } else {
                formData.set(switchInfo.name, '0');
            }
        }
    });
}

// ========== API SIMULATION ==========
async function submitTagForm(formData) {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Simulate success response
    // In real implementation, replace this with actual API call
    return {
        success: true,
        message: '标签保存成功',
        data: {
            id: 1,
            name: formData.get('name'),
            slug: formData.get('slug'),
            color: formData.get('color')
        }
    };
}

// ========== UTILITY FUNCTIONS ==========
function generateSlugFromName() {
    const tagName = document.getElementById('tagName')?.value;
    const tagSlug = document.getElementById('tagSlug');
    
    if (!tagName || !tagSlug || tagSlug.value) return;
    
    // Simple slug generation (in real app, use more sophisticated method)
    const slug = tagName
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    
    tagSlug.value = slug;
}

// Auto-generate slug when name changes (if slug is empty)
document.addEventListener('DOMContentLoaded', function() {
    const tagName = document.getElementById('tagName');
    if (tagName) {
        tagName.addEventListener('blur', generateSlugFromName);
    }
});

// ========== DEMO FUNCTIONS FOR TESTING ==========
window.tagEditDemo = {
    toggleStatus: () => window.switchAPI?.toggle('tagStatus'),
    toggleFeatured: () => window.switchAPI?.toggle('tagFeatured'),
    setRandomColor: () => {
        const colors = ['#6366f1', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        document.getElementById('tagColor').value = randomColor;
        document.getElementById('tagColorHex').value = randomColor;
        updateTagPreview();
    },
    testValidation: () => {
        document.getElementById('tagName').value = '';
        validateTagEditForm();
    },
    showSuccessAlert: () => {
        if (window.AdminCommon.SwitchUtils) {
            window.AdminCommon.SwitchUtils.showAlert('success', '这是一个成功消息！');
        }
    },
    showErrorAlert: () => {
        if (window.AdminCommon.SwitchUtils) {
            window.AdminCommon.SwitchUtils.showAlert('danger', '这是一个错误消息！');
        }
    }
};

console.log('Tag edit page script loaded. Demo functions available in window.tagEditDemo');