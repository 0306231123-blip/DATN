@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<style>
    .product-layout {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .filter-sidebar {
        width: 200px;
        flex-shrink: 0;
        background: transparent;
        padding: 0;
        border: none;
        position: sticky;
        top: 100px;
    }
    .product-main {
        flex-grow: 1;
    }
    .filter-main-title {
        font-weight: 700;
        color: #000000;
        text-transform: uppercase;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }
    .filter-title {
        font-weight: 500;
        color: #000000;
        margin-bottom: 12px;
        margin-top: 24px;
        font-size: 0.95rem;
        text-transform: capitalize;
        border: none;
        padding: 0;
    }
    .filter-item {
        margin-bottom: 8px;
    }
    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        color: #000000;
        font-weight: 400;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .filter-label:hover {
        color: #ec4899;
    }
    .filter-checkbox {
        width: 14px;
        height: 14px;
        border-radius: 2px;
        accent-color: #ec4899;
        cursor: pointer;
        border: 1px solid #d1d5db;
    }
    @media (max-width: 768px) {
        .product-layout {
            flex-direction: column;
        }
        .filter-sidebar {
            width: 100%;
            position: static;
        }
    }
    
    /* Dual Range Slider Styles */
    .range-slider {
        position: relative;
        width: 100%;
        height: 20px;
        margin-top: 10px;
        margin-bottom: 5px;
    }
    .range-slider input[type="range"] {
        position: absolute;
        width: 100%;
        pointer-events: none;
        -webkit-appearance: none;
        background: transparent;
        z-index: 2;
        margin: 0;
        top: 0;
    }
    .range-slider input[type="range"]::-webkit-slider-thumb {
        pointer-events: all;
        width: 14px;
        height: 14px;
        -webkit-appearance: none;
        background: #ec4899;
        border-radius: 50%;
        cursor: pointer;
    }
    .slider-track {
        position: absolute;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        border-radius: 2px;
        top: 6px;
        z-index: 1;
    }
    .slider-range-fill {
        position: absolute;
        height: 2px;
        background: #ec4899;
        border-radius: 2px;
        top: 6px;
        z-index: 1;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const dmCheckboxes = document.querySelectorAll('.dm-checkbox');
        const thCheckboxes = document.querySelectorAll('.th-checkbox');
        const dmInput = document.getElementById('danh_muc_input');
        const thInput = document.getElementById('thuong_hieu_input');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Danh muc
            const dmValues = [];
            dmCheckboxes.forEach(cb => {
                if (cb.checked) dmValues.push(cb.value);
            });
            dmInput.value = dmValues.join(',');
            dmCheckboxes.forEach(cb => cb.disabled = true);
            if (dmInput.value === '') dmInput.disabled = true;

            // Thuong hieu
            if (thInput) {
                const thValues = [];
                thCheckboxes.forEach(cb => {
                    if (cb.checked) thValues.push(cb.value);
                });
                thInput.value = thValues.join(',');
                thCheckboxes.forEach(cb => cb.disabled = true);
                if (thInput.value === '') thInput.disabled = true;
            }

            // Optimize URL queries
            const minPrice = form.querySelector('input[name="min_price"]');
            const maxPrice = form.querySelector('input[name="max_price"]');
            const sort = form.querySelector('select[name="sort"]');
            
            if (minPrice && minPrice.value === '') minPrice.disabled = true;
            if (maxPrice && maxPrice.value === '') maxPrice.disabled = true;
            if (sort && sort.value === '') sort.disabled = true;
            
            // disable sliders before submit so they don't appear in url
            document.getElementById('min_slider').disabled = true;
            document.getElementById('max_slider').disabled = true;

            form.submit();
        });

        // Dual Range Slider Logic
        const minSlider = document.getElementById('min_slider');
        const maxSlider = document.getElementById('max_slider');
        const minInputUI = document.getElementById('min_price_input');
        const maxInputUI = document.getElementById('max_price_input');
        const sliderFill = document.getElementById('slider-fill');

        function updateSliderFill() {
            const min = parseInt(minSlider.min);
            const max = parseInt(maxSlider.max);
            const valMin = parseInt(minSlider.value) || min;
            const valMax = parseInt(maxSlider.value) || max;
            
            const percentMin = ((valMin - min) / (max - min)) * 100;
            const percentMax = ((valMax - min) / (max - min)) * 100;
            
            if(sliderFill) {
                sliderFill.style.left = percentMin + "%";
                sliderFill.style.width = (percentMax - percentMin) + "%";
            }
        }

        if(minSlider && maxSlider) {
            updateSliderFill();

            minSlider.addEventListener('input', function() {
                if(parseInt(minSlider.value) > parseInt(maxSlider.value)) {
                    minSlider.value = maxSlider.value;
                }
                minInputUI.value = minSlider.value > 0 ? minSlider.value : '';
                updateSliderFill();
            });

            maxSlider.addEventListener('input', function() {
                if(parseInt(maxSlider.value) < parseInt(minSlider.value)) {
                    maxSlider.value = minSlider.value;
                }
                maxInputUI.value = maxSlider.value < maxSlider.max ? maxSlider.value : '';
                updateSliderFill();
            });

            minInputUI.addEventListener('input', function() {
                minSlider.value = minInputUI.value || minSlider.min;
                updateSliderFill();
            });

            maxInputUI.addEventListener('input', function() {
                maxSlider.value = maxInputUI.value || maxSlider.max;
                updateSliderFill();
            });
        }
    });
</script>
@endsection
