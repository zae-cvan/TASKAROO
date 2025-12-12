<!-- Dark Mode Partial -->
<script>
  // Apply saved theme immediately
  (function() {
      const savedTheme = localStorage.getItem("theme");
      if (savedTheme === "dark") {
          document.documentElement.classList.add("dark-mode");
      } else {
          document.documentElement.classList.remove("dark-mode");
      }
  })();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeBtn = document.getElementById('theme-toggle');
    if (!themeBtn) return;

    const icon = themeBtn.querySelector('i');
    const savedTheme = localStorage.getItem('theme');

    // Set initial icon
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        if (icon) icon.setAttribute('data-lucide','sun');
    } else {
        document.documentElement.classList.remove('dark-mode');
        if (icon) icon.setAttribute('data-lucide','moon');
    }
    if (window.lucide) lucide.createIcons();

    // Toggle dark mode
    themeBtn.addEventListener('click', function () {
        document.documentElement.classList.toggle('dark-mode'); // only html
        const isDark = document.documentElement.classList.contains('dark-mode');
        if (icon) icon.setAttribute('data-lucide', isDark ? 'sun' : 'moon');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        if (window.lucide) lucide.createIcons();
    });
});
</script>

<style>
/* Dark mode styles scoped only to html.dark-mode - Improved Contrast */
html.dark-mode {
    background-color: #0f0f0f !important;
    color: #ffffff !important;
}

/* Navigation and Layout */
html.dark-mode nav { 
    background-color: #1a1a1a !important; 
    border-color: #3a3a3a !important; 
    color: #ffffff !important;
}

html.dark-mode aside { 
    background-color: #151515 !important; 
    border-color: #3a3a3a !important; 
}

html.dark-mode main { 
    background-color: #0f0f0f !important; 
    color: #ffffff !important;
}

/* Typography - High Contrast */
html.dark-mode p, 
html.dark-mode span,
html.dark-mode label,
html.dark-mode div,
html.dark-mode h1, 
html.dark-mode h2, 
html.dark-mode h3, 
html.dark-mode h4, 
html.dark-mode h5, 
html.dark-mode h6 { 
    color: #ffffff !important; 
}

html.dark-mode .text-gray-600,
html.dark-mode .text-gray-500,
html.dark-mode .text-gray-700 {
    color: #d1d5db !important;
}

html.dark-mode .text-gray-800 {
    color: #f3f4f6 !important;
}

/* Cards, Containers, and Backgrounds */
html.dark-mode .bg-white,
html.dark-mode .bg-orange-50,
html.dark-mode .bg-gray-50,
html.dark-mode .shine-effect,
html.dark-mode .rounded-2xl,
html.dark-mode .rounded-xl,
html.dark-mode .shadow-lg,
html.dark-mode .shadow-xl,
html.dark-mode .shadow-md {
    background-color: #1f1f1f !important; 
    color: #ffffff !important;
    border-color: #3a3a3a !important;
}

html.dark-mode .bg-gradient-to-r,
html.dark-mode .bg-gradient-to-br {
    background-color: #1f1f1f !important;
}

html.dark-mode .from-white,
html.dark-mode .to-orange-50 {
    background-color: #1f1f1f !important;
}

/* Input Fields - High Contrast */
html.dark-mode input,
html.dark-mode select,
html.dark-mode textarea { 
    background-color: #2a2a2a !important; 
    color: #ffffff !important; 
    border-color: #4a4a4a !important; 
}

html.dark-mode input:focus,
html.dark-mode select:focus,
html.dark-mode textarea:focus {
    background-color: #2f2f2f !important;
    border-color: #f97316 !important;
    color: #ffffff !important;
}

html.dark-mode input::placeholder,
html.dark-mode textarea::placeholder {
    color: #9ca3af !important;
}

/* Buttons - High Contrast */
html.dark-mode button {
    color: #ffffff !important;
}

html.dark-mode .bg-gradient-to-r.from-orange-500,
html.dark-mode .bg-gradient-to-r.from-orange-600 {
    background: linear-gradient(to right, #ea580c, #c2410c) !important;
    color: #ffffff !important;
}

html.dark-mode .bg-orange-500,
html.dark-mode .bg-orange-600 {
    background-color: #ea580c !important;
    color: #ffffff !important;
}

html.dark-mode button:hover {
    opacity: 0.9;
}

/* Logout Button */
html.dark-mode #logout-btn { 
    background-color: #2a1a1a !important; 
    color: #ff6b6b !important; 
    border-color: #4a2a2a !important;
}

html.dark-mode #logout-btn:hover { 
    background-color: #3a1a1a !important; 
    color: #ff8787 !important;
}

/* Theme Toggle Button */
html.dark-mode #theme-toggle { 
    background-color: #2a2a2a !important; 
    color: #fbbf24 !important; 
    border: 1px solid #4a4a4a !important; 
}

html.dark-mode #theme-toggle:hover { 
    background-color: #3a3a3a !important; 
    color: #fcd34d !important;
}

/* Links and Navigation */
html.dark-mode a {
    color: #fbbf24 !important;
}

html.dark-mode a:hover {
    color: #fcd34d !important;
}

html.dark-mode .active-link {
    background-color: rgba(234, 88, 12, 0.4) !important;
    color: #ffedd5 !important;
    border-color: #ea580c !important;
}

html.dark-mode .active-link .indicator {
    background-color: #ea580c !important;
    opacity: 1;
}

/* Sidebar Navigation Links */
html.dark-mode aside a {
    color: #e5e7eb !important;
}

html.dark-mode aside a:hover {
    color: #ffffff !important;
    background-color: #2a2a2a !important;
}

/* Borders - More Visible */
html.dark-mode .border,
html.dark-mode .border-2,
html.dark-mode .border-b,
html.dark-mode .border-t {
    border-color: #3a3a3a !important;
}

html.dark-mode .border-orange-200 {
    border-color: #7c2d12 !important;
}

html.dark-mode .border-orange-100 {
    border-color: #9a3412 !important;
}

/* Profile Section */
html.dark-mode .bg-orange-50 {
    background-color: #2a1a0f !important;
}

html.dark-mode .bg-orange-100 {
    background-color: #3a1f0f !important;
}

html.dark-mode .text-orange-600,
html.dark-mode .text-orange-700 {
    color: #fb923c !important;
}

html.dark-mode .text-orange-500 {
    color: #f97316 !important;
}

/* Flash Messages */
html.dark-mode .bg-orange-100 {
    background-color: #3a1f0f !important;
    border-color: #7c2d12 !important;
}

html.dark-mode .text-orange-800 {
    color: #fb923c !important;
}

html.dark-mode .bg-red-100 {
    background-color: #3a1a1a !important;
    border-color: #7c1a1a !important;
}

html.dark-mode .text-red-800 {
    color: #fca5a5 !important;
}

/* Task Cards */
html.dark-mode .task-card {
    background-color: #1f1f1f !important;
    border-color: #3a3a3a !important;
    color: #ffffff !important;
}

html.dark-mode .task-card:hover {
    background-color: #252525 !important;
    border-color: #4a4a4a !important;
}

/* Modals */
html.dark-mode .fixed.inset-0 {
    background-color: rgba(0, 0, 0, 0.8) !important;
}

html.dark-mode .bg-white.rounded-2xl,
html.dark-mode .bg-white.rounded-3xl,
html.dark-mode .bg-white.rounded-xl {
    background-color: #1f1f1f !important;
    color: #ffffff !important;
    border-color: #3a3a3a !important;
}

/* Tables */
html.dark-mode table {
    color: #ffffff !important;
}

html.dark-mode thead {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
}

html.dark-mode tbody tr {
    background-color: #1f1f1f !important;
    border-color: #3a3a3a !important;
}

html.dark-mode tbody tr:hover {
    background-color: #252525 !important;
}

/* Dropdowns and Selects */
html.dark-mode select {
    background-color: #2a2a2a !important;
    color: #ffffff !important;
    border-color: #4a4a4a !important;
}

html.dark-mode select option {
    background-color: #2a2a2a !important;
    color: #ffffff !important;
}

/* Checkboxes and Radio Buttons */
html.dark-mode input[type="checkbox"],
html.dark-mode input[type="radio"] {
    accent-color: #ea580c !important;
}

/* Badges and Tags */
html.dark-mode .bg-orange-200 {
    background-color: #4a2a0f !important;
    color: #fb923c !important;
}

html.dark-mode .text-orange-700 {
    color: #fb923c !important;
}

/* Icons */
html.dark-mode i[data-lucide] {
    color: inherit;
}

/* Scrollbar */
html.dark-mode ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

html.dark-mode ::-webkit-scrollbar-track {
    background: #1a1a1a;
}

html.dark-mode ::-webkit-scrollbar-thumb {
    background: #3a3a3a;
    border-radius: 4px;
}

html.dark-mode ::-webkit-scrollbar-thumb:hover {
    background: #4a4a4a;
}

/* Gradient Backgrounds - High Contrast */
html.dark-mode .bg-gradient-to-r.from-white.to-orange-50,
html.dark-mode .bg-gradient-to-br.from-white.to-orange-50,
html.dark-mode .bg-gradient-to-br.from-orange-50.to-orange-50 {
    background: linear-gradient(to right, #1f1f1f, #2a1a0f) !important;
    background: linear-gradient(to bottom right, #1f1f1f, #2a1a0f) !important;
    color: #ffffff !important;
}

html.dark-mode .from-orange-50,
html.dark-mode .to-orange-50 {
    background-color: #2a1a0f !important;
}

/* Notification Cards */
html.dark-mode .bg-gradient-to-r.from-white.to-orange-50.rounded-2xl,
html.dark-mode .bg-gradient-to-r.from-white.to-orange-50.rounded-3xl {
    background: linear-gradient(to right, #1f1f1f, #2a1a0f) !important;
    border-color: #4a2a0f !important;
    color: #ffffff !important;
}

html.dark-mode .bg-gradient-to-r.from-white.to-orange-50:hover {
    background: linear-gradient(to right, #252525, #3a2a1f) !important;
    border-color: #5a3a1f !important;
}

/* Dashboard Cards and Containers */
html.dark-mode .container {
    color: #ffffff !important;
}

html.dark-mode .max-w-4xl,
html.dark-mode .max-w-7xl {
    color: #ffffff !important;
}

/* Task Card Specific Styles */
html.dark-mode .task-card.bg-white {
    background-color: #1f1f1f !important;
    border-color: #3a3a3a !important;
}

html.dark-mode .task-card:hover {
    background: linear-gradient(to bottom right, #252525, #2a1a0f) !important;
    border-color: #4a4a4a !important;
}

html.dark-mode .task-card .text-gray-800 {
    color: #f3f4f6 !important;
}

html.dark-mode .task-card .text-gray-600 {
    color: #d1d5db !important;
}

/* Header Cards with Gradients */
html.dark-mode .bg-gradient-to-r.from-white.to-orange-50.rounded-3xl {
    background: linear-gradient(to right, #1f1f1f, #2a1a0f) !important;
    border-color: #4a2a0f !important;
}

html.dark-mode .bg-gradient-to-r.from-orange-600.to-orange-500 {
    background: linear-gradient(to right, #ea580c, #c2410c) !important;
    color: #ffffff !important;
}

html.dark-mode .bg-gradient-to-r.from-orange-500.to-orange-600 {
    background: linear-gradient(to right, #ea580c, #c2410c) !important;
    color: #ffffff !important;
}

html.dark-mode .bg-gradient-to-r.from-orange-500.to-orange-600:hover {
    background: linear-gradient(to right, #c2410c, #9a3412) !important;
}

/* Empty State Cards */
html.dark-mode .bg-white.rounded-3xl.text-center {
    background-color: #1f1f1f !important;
    border-color: #3a3a3a !important;
    color: #ffffff !important;
}

html.dark-mode .bg-white.rounded-3xl.text-center .text-gray-700 {
    color: #f3f4f6 !important;
}

html.dark-mode .bg-white.rounded-3xl.text-center .text-gray-500 {
    color: #d1d5db !important;
}

/* Icon Backgrounds */
html.dark-mode .bg-gradient-to-br.from-orange-400.to-orange-600 {
    background: linear-gradient(to bottom right, #ea580c, #c2410c) !important;
}

html.dark-mode .bg-gradient-to-br.from-orange-100.to-orange-200 {
    background: linear-gradient(to bottom right, #3a1f0f, #4a2a0f) !important;
}

html.dark-mode .bg-gradient-to-br.from-orange-100.to-orange-100 {
    background: linear-gradient(to bottom right, #3a1f0f, #3a1f0f) !important;
}

/* Text Gradient */
html.dark-mode .bg-gradient-to-r.from-orange-600.to-orange-500.bg-clip-text.text-transparent {
    background: linear-gradient(to right, #fb923c, #f97316) !important;
    -webkit-background-clip: text !important;
    background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    color: #fb923c !important;
}

/* Profile Card in Sidebar */
html.dark-mode .bg-orange-50.rounded-2xl.shadow-lg {
    background-color: #2a1a0f !important;
    border-color: #4a2a0f !important;
}

html.dark-mode .bg-orange-50.rounded-2xl.shadow-lg .text-gray-800 {
    color: #f3f4f6 !important;
}

html.dark-mode .bg-orange-50.rounded-2xl.shadow-lg .text-gray-500 {
    color: #d1d5db !important;
}

html.dark-mode .bg-orange-200 {
    background-color: #4a2a0f !important;
}

html.dark-mode .bg-orange-200 .text-orange-700 {
    color: #fb923c !important;
}

/* Ring Colors */
html.dark-mode .ring-orange-200 {
    --tw-ring-color: #7c2d12 !important;
}

/* Shadow Improvements */
html.dark-mode .shadow-lg,
html.dark-mode .shadow-xl,
html.dark-mode .shadow-2xl {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3) !important;
}

html.dark-mode .shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
}

/* Backdrop */
html.dark-mode .backdrop-blur {
    background-color: rgba(0, 0, 0, 0.7) !important;
}

html.dark-mode .bg-black.bg-opacity-20 {
    background-color: rgba(0, 0, 0, 0.6) !important;
}

html.dark-mode .bg-black.bg-opacity-60 {
    background-color: rgba(0, 0, 0, 0.8) !important;
}
</style>
