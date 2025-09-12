@extends('memberDashboard.layout')

@section('title', 'Appearance Settings')

@section('content')
<div class="p-6" x-data="appearanceSettings()">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Appearance Settings</h1>
        <p class="text-gray-600">Customize your dashboard theme, colors, and layout preferences.</p>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Theme Settings -->
        <div class="settings-section">
            <h3 class="text-xl font-semibold mb-6">Theme Settings</h3>
            
            <!-- Theme Mode -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Theme Mode</label>
                <div class="theme-toggle-group">
                    <button @click="themeMode = 'light'; applyTheme();" 
                            :class="themeMode === 'light' ? 'theme-toggle-btn active' : 'theme-toggle-btn'"
                            class="theme-toggle-btn">
                        <i class="fas fa-sun mr-2"></i> Light Mode
                    </button>
                    <button @click="themeMode = 'dark'; applyTheme();" 
                            :class="themeMode === 'dark' ? 'theme-toggle-btn active' : 'theme-toggle-btn'"
                            class="theme-toggle-btn">
                        <i class="fas fa-moon mr-2"></i> Dark Mode
                    </button>
                    <button @click="themeMode = 'auto'; applyTheme();" 
                            :class="themeMode === 'auto' ? 'theme-toggle-btn active' : 'theme-toggle-btn'"
                            class="theme-toggle-btn">
                        <i class="fas fa-adjust mr-2"></i> Auto (System)
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">Auto mode will follow your system's preference</p>
            </div>

            <!-- Accent Color -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Accent Color</label>
                <div class="grid grid-cols-5 gap-3 mb-4">
                    <template x-for="color in accentColors" :key="color.value">
                        <button @click="accentColor = color.value; applyTheme()" 
                                :class="accentColor === color.value ? 'accent-swatch active' : 'accent-swatch'"
                                class="accent-swatch w-12 h-12"
                                :style="`background: ${color.value}`" 
                                :title="color.name"></button>
                    </template>
                </div>
                
                <!-- Custom Color Picker -->
                <div class="flex items-center gap-3">
                    <input type="color" x-model="accentColor" @change="applyTheme()" 
                           class="w-12 h-8 rounded border border-gray-300 cursor-pointer">
                    <span class="text-sm text-gray-600">Custom color</span>
                </div>
            </div>
        </div>

        <!-- Layout Settings -->
        <div class="settings-section">
            <h3 class="text-xl font-semibold mb-6">Layout Settings</h3>
            
            <!-- Sidebar Position -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Sidebar Position</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="sidebarPos" value="left" x-model="sidebarPos" @change="applyTheme()" class="text-red-500">
                        <span class="text-sm">Left Side</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="sidebarPos" value="right" x-model="sidebarPos" @change="applyTheme()" class="text-red-500">
                        <span class="text-sm">Right Side</span>
                    </label>
                </div>
            </div>

            <!-- Sidebar Style -->
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="sidebarCompact" @change="applyTheme()" class="rounded text-red-500">
                    <div>
                        <div class="text-sm font-medium text-gray-700">Compact Sidebar</div>
                        <div class="text-sm text-gray-500">Show only icons in the sidebar</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8 p-6 bg-gray-50 rounded-lg">
        <div class="flex gap-3">
            <button @click="savePreferences()" 
                    :disabled="saving"
                    class="bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>
                <span x-text="saving ? 'Saving...' : 'Save Preferences'"></span>
            </button>
            
            <button @click="resetToDefaults()" 
                    class="border border-gray-300 hover:bg-gray-50 px-6 py-2 rounded-lg text-gray-700 transition-colors">
                <i class="fas fa-undo mr-2"></i>Reset to Defaults
            </button>
        </div>
        
        <div class="flex gap-3">
            <button @click="exportSettings()" 
                    class="text-blue-600 hover:text-blue-700 px-4 py-2 text-sm">
                <i class="fas fa-download mr-1"></i>Export Settings
            </button>
            <button @click="showImportModal = true" 
                    class="text-blue-600 hover:text-blue-700 px-4 py-2 text-sm">
                <i class="fas fa-upload mr-1"></i>Import Settings
            </button>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="showImportModal" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showImportModal = false">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Import Settings</h3>
            <textarea x-model="importData" 
                      placeholder="Paste exported settings JSON here..."
                      class="w-full h-32 p-3 border border-gray-300 rounded-lg resize-none text-sm"
                      rows="6"></textarea>
            <div class="flex justify-end gap-3 mt-4">
                <button @click="showImportModal = false" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Cancel
                </button>
                <button @click="importSettings()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Import
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function appearanceSettings() {
    return {
        // State
        themeMode: 'auto',
        accentColor: '#ef4444',
        sidebarPos: 'left',
        sidebarCompact: false,
        saving: false,
        showImportModal: false,
        importData: '',
        
        // Predefined accent colors
        accentColors: [
            { name: 'Red', value: '#ef4444' },
            { name: 'Blue', value: '#3b82f6' },
            { name: 'Green', value: '#10b981' },
            { name: 'Orange', value: '#f59e0b' },
            { name: 'Purple', value: '#8b5cf6' },
            { name: 'Pink', value: '#ec4899' },
            { name: 'Indigo', value: '#6366f1' },
            { name: 'Teal', value: '#14b8a6' },
            { name: 'Yellow', value: '#eab308' },
            { name: 'Slate', value: '#64748b' }
        ],
        
        init() {
            this.loadPreferences();
            this.applyTheme();
        },

        async loadPreferences() {
            try {
                const response = await fetch('/api/user/preferences/appearance', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.appearance) {
                        this.themeMode = data.appearance.theme_mode || 'auto';
                        this.accentColor = data.appearance.accent_color || '#ef4444';
                        this.sidebarPos = data.appearance.sidebar_position || 'left';
                        this.sidebarCompact = data.appearance.sidebar_compact || false;
                    }
                } else {
                    this.loadFromLocalStorage();
                }
            } catch (error) {
                console.log('Loading preferences from localStorage due to:', error);
                this.loadFromLocalStorage();
            }
        },

        loadFromLocalStorage() {
            this.themeMode = localStorage.getItem('themeMode') || 'auto';
            this.accentColor = localStorage.getItem('accentColor') || '#ef4444';
            this.sidebarPos = localStorage.getItem('sidebarPos') || 'left';
            this.sidebarCompact = localStorage.getItem('sidebarCompact') === 'true';
        },

        async savePreferences() {
            this.saving = true;
            const preferences = {
                theme_mode: this.themeMode,
                accent_color: this.accentColor,
                sidebar_position: this.sidebarPos,
                sidebar_compact: this.sidebarCompact
            };

            try {
                const response = await fetch('/api/user/preferences', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        key: 'appearance',
                        value: preferences
                    })
                });

                if (response.ok) {
                    this.showNotification('Preferences saved successfully!', 'success');
                } else {
                    this.showNotification('Error saving preferences', 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showNotification('Error saving preferences', 'error');
            }

            this.saving = false;
        },

        async resetToDefaults() {
            if (confirm('Reset all appearance settings to defaults?')) {
                this.themeMode = 'auto';
                this.accentColor = '#ef4444';
                this.sidebarPos = 'left';
                this.sidebarCompact = false;
                
                this.applyTheme();
                await this.savePreferences();
            }
        },

        applyTheme() {
            const root = document.documentElement;
            
            root.style.setProperty('--accent-color', this.accentColor);
            
            if (this.themeMode === 'dark') {
                root.classList.add('theme-dark');
            } else if (this.themeMode === 'light') {
                root.classList.remove('theme-dark');
            } else {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    root.classList.add('theme-dark');
                } else {
                    root.classList.remove('theme-dark');
                }
            }
            
            root.setAttribute('data-sidebar-pos', this.sidebarPos);
            if (this.sidebarCompact) {
                root.classList.add('sidebar-compact');
            } else {
                root.classList.remove('sidebar-compact');
            }

            localStorage.setItem('themeMode', this.themeMode);
            localStorage.setItem('accentColor', this.accentColor);
            localStorage.setItem('sidebarPos', this.sidebarPos);
            localStorage.setItem('sidebarCompact', this.sidebarCompact);
        },

        exportSettings() {
            const settings = {
                theme_mode: this.themeMode,
                accent_color: this.accentColor,
                sidebar_position: this.sidebarPos,
                sidebar_compact: this.sidebarCompact,
                exported_at: new Date().toISOString()
            };
            
            const dataStr = JSON.stringify(settings, null, 2);
            const dataBlob = new Blob([dataStr], {type:'application/json'});
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = 'pulseone-appearance-settings.json';
            link.click();
        },

        async importSettings() {
            try {
                const settings = JSON.parse(this.importData);
                
                if (settings.theme_mode) this.themeMode = settings.theme_mode;
                if (settings.accent_color) this.accentColor = settings.accent_color;
                if (settings.sidebar_position) this.sidebarPos = settings.sidebar_position;
                if (typeof settings.sidebar_compact === 'boolean') this.sidebarCompact = settings.sidebar_compact;
                
                this.applyTheme();
                await this.savePreferences();
                
                this.showImportModal = false;
                this.importData = '';
                this.showNotification('Settings imported successfully!', 'success');
            } catch (error) {
                this.showNotification('Invalid settings format', 'error');
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'warning' ? 'bg-yellow-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endsection
