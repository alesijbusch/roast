window.addEventListener('load', function () {
    // countdown
    let countdowns = document.querySelectorAll('.js-countdown');
    countdowns.forEach(countdown => {
        let endDate = countdown.getAttribute('data-active-to'),
            countdownDays = countdown.querySelectorAll('.days')[0],
            countdownHours = countdown.querySelectorAll('.hours')[0],
            countdownMinutes = countdown.querySelectorAll('.minutes')[0],
            // countdownSec = countdown.querySelectorAll('.sec')[0],
            deadline = new Date(endDate).getTime();

        let x = setInterval(function () {
            let now = new Date().getTime(),
                t = deadline - now,
                days = Math.floor(t / (1000 * 60 * 60 * 24)),
                hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60)),
                seconds = Math.floor((t % (1000 * 60)) / 1000);

            countdownDays.innerHTML = days+'д';
            countdownHours.innerHTML = hours+'ч';
            countdownMinutes.innerHTML = minutes+'м';
            // countdownSec.innerHTML = seconds+'с';

            if (t < 0) {
                clearInterval(x);
                countdownDays.innerHTML = '0д';
                countdownHours.innerHTML = '0ч';
                countdownMinutes.innerHTML = '0м';
                // countdownSec.innerHTML = ': 0с';
            }
        }, 1000);
    });
})

