/**
 * Admin Switch - 管理后台开关组件
 *
 * 依赖：无
 *
 * 提供功能：
 * - 自定义开关控件初始化
 * - 开关状态管理
 * - 开关交互处理
 * - Switch API 接口
 */

(function() {
    'use strict';

    // ========== COMMON SWITCH FUNCTIONALITY ==========
    /* 通用的开关控件功能，适用于标签编辑等页面 */

    /**
     * Initialize switches by reading their HTML checkbox attributes
     * This allows server-side templates to set initial states
     */
    function initializeSwitches() {
        console.log('Initializing switches by reading HTML checkbox attributes...');

        // Find all custom switches on the page
        const switches = document.querySelectorAll('.custom-switch input[type="checkbox"]');

        switches.forEach(checkbox => {
            const switchId = checkbox.id;
            if (switchId) {
                // Read the current checked state from the HTML attribute
                const isChecked = checkbox.checked;

                console.log(`Switch ${switchId}: HTML checkbox checked = ${isChecked}`);

                // Set the visual state based on the checkbox state
                setSwitchVisualState(switchId, isChecked);
            }
        });

        console.log('All switches initialized from HTML checkbox attributes');
    }

    /**
     * Set switch visual state based on checkbox value
     * This function only updates the visual appearance, not the checkbox state
     */
    function setSwitchVisualState(switchId, isChecked) {
        const checkbox = document.getElementById(switchId);
        if (!checkbox) return;

        const switchElement = checkbox.closest('.custom-switch');
        const slider = switchElement.querySelector('.switch-slider');

        // Update visual appearance based on checkbox state
        if (isChecked) {
            slider.style.backgroundColor = 'var(--accent-primary)';
            slider.style.setProperty('--switch-translate', 'translateX(24px)');
        } else {
            slider.style.backgroundColor = 'var(--border-medium)';
            slider.style.setProperty('--switch-translate', 'translateX(0)');
        }

        console.log(`Switch ${switchId} visual state set to: ${isChecked ? 'ON' : 'OFF'}`);
    }

    /**
     * Set switch value (both checkbox and visual state)
     * This function updates both the checkbox checked property and visual state
     */
    function setSwitchValue(switchId, value) {
        const checkbox = document.getElementById(switchId);
        if (!checkbox) return;

        // Set checkbox state
        checkbox.checked = value;

        //set hide input value for submit
        // 通过 class 查找同级元素
        const siblingsByClass = checkbox.parentElement.querySelectorAll('.ee_switch-value');
        if (siblingsByClass[0]){
            siblingsByClass[0].value= value?1:0;
        }

        // Update visual state
        setSwitchVisualState(switchId, value);

        console.log(`Switch ${switchId} value set to: ${value ? 'ON' : 'OFF'}`);
    }

    /**
     * Toggle switch value
     */
    function toggleSwitch(switchId) {
        const checkbox = document.getElementById(switchId);
        if (!checkbox) return false;

        const switchGroup = checkbox.closest('.switch-group');

        // Check if switch is disabled
        if (checkbox.disabled || switchGroup.classList.contains('disabled')) {
            console.log(`Switch ${switchId} is disabled, cannot toggle`);
            return false;
        }

        // Toggle the value
        const newValue = !checkbox.checked;
        setSwitchValue(switchId, newValue);

        // Trigger change event for form validation/handling
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
        console.log(`Switch ${switchId} toggled to: ${newValue ? 'ON' : 'OFF'}`);
        return true;
    }

    /**
     * Setup switch click handlers
     */
    function setupSwitchInteraction(switchId) {
        const checkbox = document.getElementById(switchId);
        if (!checkbox) return;

        const switchElement = checkbox.closest('.custom-switch');
        const switchGroup = checkbox.closest('.switch-group');
        const slider = switchElement.querySelector('.switch-slider');
        const label = switchGroup.querySelector('.switch-label');

        // Handle click on slider
        if (slider) {
            slider.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSwitch(switchId);
            });
        }

        // Handle click on label
        if (label) {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSwitch(switchId);
            });
        }

        // Handle direct checkbox change (for programmatic changes or accessibility)
        checkbox.addEventListener('change', function() {
            setSwitchVisualState(switchId, this.checked);
        });

        console.log(`Switch interaction setup completed for: ${switchId}`);
    }

    /**
     * Setup switch interactions for all switches on the page
     */
    function setupAllSwitchInteractions() {
        const switches = document.querySelectorAll('.custom-switch input[type="checkbox"]');

        switches.forEach(checkbox => {
            const switchId = checkbox.id;
            if (switchId) {
                setupSwitchInteraction(switchId);
            }
        });

        console.log(`Setup interaction for ${switches.length} switches`);
    }

    /**
     * API for external control of switches
     */
    const switchAPI = {
        setValue: (switchId, value) => setSwitchValue(switchId, value),
        getValue: (switchId) => {
            const checkbox = document.getElementById(switchId);
            return checkbox ? checkbox.checked : false;
        },
        toggle: (switchId) => toggleSwitch(switchId),
        setEnabled: function(switchId, enabled) {
            const checkbox = document.getElementById(switchId);
            if (!checkbox) return;

            const switchGroup = checkbox.closest('.switch-group');
            const switchElement = checkbox.closest('.custom-switch');

            checkbox.disabled = !enabled;

            if (enabled) {
                switchGroup.classList.remove('disabled');
                switchElement.classList.remove('disabled');
            } else {
                switchGroup.classList.add('disabled');
                switchElement.classList.add('disabled');
            }

            console.log(`Switch ${switchId} is now ${enabled ? 'enabled' : 'disabled'}`);
        },
        isEnabled: (switchId) => {
            const checkbox = document.getElementById(switchId);
            return checkbox ? !checkbox.disabled : false;
        }
    };

    /**
     * Alert message function for forms
     */
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        alertContainer.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);

        // Scroll to top to show alert
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    if (!window.AdminCommon.SwitchUtils) {
        window.AdminCommon.SwitchUtils = {};
    }

    window.AdminCommon.SwitchUtils.initializeSwitches = initializeSwitches;
    window.AdminCommon.SwitchUtils.setSwitchVisualState = setSwitchVisualState;
    window.AdminCommon.SwitchUtils.setSwitchValue = setSwitchValue;
    window.AdminCommon.SwitchUtils.toggleSwitch = toggleSwitch;
    window.AdminCommon.SwitchUtils.setupSwitchInteraction = setupSwitchInteraction;
    window.AdminCommon.SwitchUtils.setupAllSwitchInteractions = setupAllSwitchInteractions;
    window.AdminCommon.SwitchUtils.showAlert = showAlert;

    window.switchAPI = switchAPI;

    // 页面加载时自动初始化
    document.addEventListener('DOMContentLoaded', function() {
        initializeSwitches();
        setupAllSwitchInteractions();
    });

    console.log('Admin Switch 已加载');
})();
