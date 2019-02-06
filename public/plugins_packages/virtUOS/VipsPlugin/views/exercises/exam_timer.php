<div id="timer" class="exam_timer">
    <?=_vips('Restzeit')?>: 0:00 h
</div>

<script>
    // compute difference between server and client time
    var serverOffset = Math.floor(new Date().getTime() / 1000) - <?= $current_time ?>;
    var timerID = setInterval(showtime, 1000);

    function showtime() {
        var timer = jQuery('#timer');
        var timestamp = Math.floor(new Date().getTime() / 1000) - serverOffset;
        var remainingSeconds = <?= $hand_in_time ?> - timestamp;
        var remainingHours = Math.floor(Math.round(remainingSeconds / 60) / 60);
        var remainingMinutes = Math.floor(Math.round(remainingSeconds / 60) % 60);

        if (remainingMinutes < 10) {
            remainingMinutes = '0' + remainingMinutes;
        }

        // update timer
        timer.text('<?= _vips('Restzeit') ?>: ' + remainingHours + ':' + remainingMinutes + ' h');

        if (remainingSeconds < 180 && !timer.hasClass('alert')) {
            timer.addClass('alert');
        }

        if (remainingSeconds < 0 && document.jsfrm) {
            clearInterval(timerID);
            document.jsfrm.removeAttribute('data-secure');
            document.jsfrm.forced.value = 1;
            document.jsfrm.submit();
        }
    }

    jQuery('#timer').draggable();
    showtime();
</script>
