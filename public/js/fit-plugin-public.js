jQuery(document).ready(function ($) {
    filter_rooms();
    filter_trainers();

    $('#filter_by_room').on('change', function () {
        filter_rooms()
    });

    $('#filter_by_trainer').on('change', function () {
        filter_trainers();
    });

    $('.event-wrp').on('click', function () {
        openModal($(this).data('event-id'), 'book');
    });

    function filter_rooms() {
        $.each($('.room_wrp'), function () {
            let id = $('#filter_by_room').find(':selected').data('id');
            if ($(this).data('id') == id) {
                $(this).removeClass('hidden')
            } else {
                $(this).addClass('hidden')
            }
        })
    }

    function filter_trainers() {
        $.each($('.event-wrp'), function () {

            let id = $('#filter_by_trainer').find(':selected').data('id');

            if ($(this).data('trainer-id') == id || id == 0) {
                $(this).addClass('d-flex');
            } else {
                $(this).removeClass('d-flex').hide();
            }
        })
    }

    let carousel = $(".card-group");



    carousel.owlCarousel({
        nav: true,
        dots: false,
        margin: 15
    });

    let y = new Intl.DateTimeFormat('en', {year: '2-digit'}).format(Date.now());
    let m = new Intl.DateTimeFormat('en', {month: '2-digit'}).format(Date.now());
    let d = new Intl.DateTimeFormat('en', {day: '2-digit'}).format(Date.now());

    $.each($('.room_wrp:not(.hidden) .owl-item'), function (index, val) {
        if ($(this).find('.item').data('date') == y + m + d) {
            carousel.trigger('to.owl.carousel', index);
        }
    });

    function openModal(args, state) {

        let dialogModal = getModal();

        // Init the modal if it hasn't been already.
        if (!dialogModal) {
            dialogModal = initModal();
        }


        let html =
            '<div class="modal-header">' +
            '<h3 class="modal-title" id="dialoglLabel"> </h3>' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="container-fluid">' +
            '<div class="row">' +
            '<div class="col-sm-3 modal-sidebar">' +
            '<div class="img-holder row"> ' +
            '</div>' +
            '<h2 class="room-title"></h2>' +
            '<h3 class="room-trainer"></h3>' +
            '<hr />' +
            '<h5 class="event-duration"></h5>' +
            '</div>' +
            '<div class="col-sm-9 place-take d-flex align-content-center justify-content-center">' +
            '<div class="trainer-place">' +
            'Trainer Place' +
            '</div>' +
            '<div id="holder" class="variant-place-selector my-2">' +
            '<ul id="places" class="p-2">' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" id="submit_event" class="btn btn-primary">Book Place</button>' +
            '</div>';

        setModalContent(html);


        $(dialogModal).modal('show');

        let rows, cols;

        if (state == 'book') {
            $.ajax({
                url: fit.ajaxurl,
                type: 'POST',
                cache: false,
                data: {
                    action: 'get_event',
                    nonce: fit.nonce,
                    id: args
                },
                success: function (out) {
                    console.log(out);

                    $.each(fit.rooms, function (index, val) {
                        if (val.ID == out.data.room_id) {
                            $(dialogModal).find('.room-title').html(val.post_title);
                        }
                    });

                    $.each(fit.trainers, function (index, val) {
                        if (val.ID == out.data.trainer_id) {
                            $(dialogModal).find('.room-trainer').html(val.post_title);
                        }
                    });

                    $(dialogModal).find('.modal-title').html(out.data.title);


                    let s = new Date(out.data.start);
                    let e = new Date(out.data.end);
                    let start_h = new Intl.DateTimeFormat('en', {
                        hour: '2-digit',
                        'hour12': false
                    }).format(new Date(out.data.start));
                    let start_m = new Intl.DateTimeFormat('en', {minute: '2-digit'}).format(new Date(out.data.start));

                    let end_h = new Intl.DateTimeFormat('en', {
                        hour: '2-digit',
                        'hour12': false
                    }).format(new Date(out.data.end));
                    let end_m = new Intl.DateTimeFormat('en', {minute: '2-digit'}).format(new Date(out.data.end));

                    $(dialogModal).find('.event-duration').html('Duration: ' + start_h + ':' + start_m + ' - ' + end_h + ':' + end_m);


                    let settings = {
                        rowCssPrefix: 'row-',
                        colCssPrefix: 'col-',
                        placeWidth: 60,
                        placeHeight: 60,
                    };

                    let reservedPlaces = out.data.places_pool;

                    $.each(fit.rooms, function (index, val) {
                        if (val.ID == out.data.room_id) {
                            cols = val.pool_capacity[0];
                            rows = val.pool_capacity[1];
                        }
                    });

                    let str = [], placeNo, className;
                    for (j = 0; j < cols; j++) {
                        for (i = 0; i < rows; i++) {
                            placeNo = (i + j * rows + 1);
                            className = 'place' + ' ' + settings.rowCssPrefix + i.toString() + ' ' + settings.colCssPrefix + j.toString();
                            if (jQuery.inArray(placeNo + '', reservedPlaces) !== -1) {
                                className += ' selectedPlace';
                            }
                            str.push('<li class="' + className + '"' +
                                'style="top:' + (j * settings.placeHeight).toString() + 'px;left:' + (i * settings.placeWidth).toString() + 'px">' +
                                '<a title="' + placeNo + '">' + placeNo + '</a>' +
                                '</li>');
                        }
                    }

                    $(dialogModal).find('#places').html(str.join(''));
                    $(dialogModal).find('#places').css('width', rows * settings.placeWidth);

                    $(dialogModal).find('#places').on('click', '.place', function () {
                        if ($(this).hasClass('selectedPlace')) {
                            alert('This place is already reserved');
                        } else {
                            $(this).toggleClass('selectingPlace');
                        }
                    });


                },
                error: function (err) {
                    alert(err);
                }
            });
        }

    }

    function getModal() {
        return document.getElementById('dialogModal');
    }

    function setModalContent(html) {
        getModal().querySelector('.modal-content').innerHTML = html;
    }

    function initModal() {
        let modal = document.createElement('div');
        modal.classList.add('modal', 'fade');
        modal.setAttribute('id', 'dialogModal');
        modal.setAttribute('tabindex', '-1');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-labelledby', 'dialogModalLabel');
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML =
            '<div class="modal-dialog modal-xl  modal-dialog-centered book-dialog" role="document">' +
            '<div class="modal-content book-content"></div>' +
            '</div>';
        document.body.appendChild(modal);
        return modal;
    }

    /*--- booking place selector ---*/

});