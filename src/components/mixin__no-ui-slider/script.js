function rangeSlider(wrapper) {
    let stepsSlider = wrapper.querySelectorAll('.no-ui-slider__ranges')[0],
        input= wrapper.closest('.popup-range').querySelector('.popup-range__title span'),
        // inputFrom = wrapper.querySelectorAll('.no-ui-slider__input-from')[0],
        // inputTo = wrapper.querySelectorAll('.no-ui-slider__input-to')[0],
        // inputs = [inputFrom, inputTo],
        minValue = wrapper.getAttribute('data-min'),
        maxValue = wrapper.getAttribute('data-max'),
        startValue = [wrapper.getAttribute('data-from'), wrapper.getAttribute('data-to')];

    console.log(input)
    var formatForSlider = {
        from: function (formattedValue) {
            return Number(formattedValue);
        },
        to: function (numericValue) {
            return Math.round(numericValue);
        }
    };

    var formatPercent = {
        from: function (value) {
            return value + '%';
        },
        to: function (value) {
            return value + '%';
        }
    }

    function filterPips(value, type) {
        return 1
    }


    noUiSlider.create(stepsSlider, {
        start: minValue,
        connect: 'lower',
        range: {
            'min': Number(minValue),
            'max': Number(maxValue)
        },
        step: 1,
        pips: {mode: 'steps', density: 50, filter: filterPips, format: formatPercent},
        format: formatForSlider,
    });

    stepsSlider.noUiSlider.on('update', function (values, handle) {
        let {options} = this
        let index = values/options.step
        input.innerHTML = values;
        let markers = wrapper.querySelectorAll('.noUi-marker')

        markers.forEach((el, i) => {
            i<index
                ? markers[i].classList.add('marked')
                : markers[i].classList.remove('marked')
        })


    });

    // Listen to keydown events on the input field.
    // inputs.forEach(function (input, handle) {
    //
    //     input.addEventListener('change', function () {
    //         stepsSlider.noUiSlider.setHandle(handle, this.value);
    //         console.log('change')
    //     });
    //
    //     input.addEventListener('keydown', function (e) {
    //
    //         var values = stepsSlider.noUiSlider.get();
    //         var value = Number(values[handle]);
    //
    //         // [[handle0_down, handle0_up], [handle1_down, handle1_up]]
    //         var steps = stepsSlider.noUiSlider.steps();
    //
    //         // [down, up]
    //         var step = steps[handle];
    //
    //         var position;
    //
    //         // 13 is enter,
    //         // 38 is key up,
    //         // 40 is key down.
    //         switch (e.which) {
    //
    //             case 13:
    //                 stepsSlider.noUiSlider.setHandle(handle, this.value);
    //                 break;
    //
    //             case 38:
    //
    //                 // Get step to go increase slider value (up)
    //                 position = step[1];
    //
    //                 // false = no step is set
    //                 if (position === false) {
    //                     position = 1;
    //                 }
    //
    //                 // null = edge of slider
    //                 if (position !== null) {
    //                     stepsSlider.noUiSlider.setHandle(handle, value + position);
    //                 }
    //
    //                 break;
    //
    //             case 40:
    //
    //                 position = step[0];
    //
    //                 if (position === false) {
    //                     position = 1;
    //                 }
    //
    //                 if (position !== null) {
    //                     stepsSlider.noUiSlider.setHandle(handle, value - position);
    //                 }
    //
    //                 break;
    //         }
    //     });
    // });

    return stepsSlider;
}


window.addEventListener('load', function () {
    document.querySelectorAll('.js-no-ui-slider').forEach(slider => {
        rangeSlider(slider);
    });

});


