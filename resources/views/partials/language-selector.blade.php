<!-- Sélecteur de langue flottant -->
<div class="language-selector" style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    background: white;
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 5px;
">
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" 
       style="
           display: inline-block;
           padding: 8px 15px;
           margin: 0 2px;
           text-decoration: none;
           border-radius: 20px;
           font-weight: 500;
           transition: all 0.3s ease;
           {{ request('lang', 'ar') === 'ar' ? 'background: #4e73df; color: white;' : 'color: #6c757d;' }}
       "
       onmouseover="if (!this.classList.contains('active')) { this.style.background='#f8f9fc'; this.style.color='#4e73df'; }"
       onmouseout="if (!this.classList.contains('active')) { this.style.background='transparent'; this.style.color='#6c757d'; }"
       class="{{ request('lang', 'ar') === 'ar' ? 'active' : '' }}">
        العربية
    </a>
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'fr']) }}" 
       style="
           display: inline-block;
           padding: 8px 15px;
           margin: 0 2px;
           text-decoration: none;
           border-radius: 20px;
           font-weight: 500;
           transition: all 0.3s ease;
           {{ request('lang') === 'fr' ? 'background: #4e73df; color: white;' : 'color: #6c757d;' }}
       "
       onmouseover="if (!this.classList.contains('active')) { this.style.background='#f8f9fc'; this.style.color='#4e73df'; }"
       onmouseout="if (!this.classList.contains('active')) { this.style.background='transparent'; this.style.color='#6c757d'; }"
       class="{{ request('lang') === 'fr' ? 'active' : '' }}">
        Français
    </a>
</div>
