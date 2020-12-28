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
        openModal($(this).data('event-id'), $(this).data('product'), $(this).data('price'), 'book');
    });

    function filter_rooms() {
        $.each($('.room_wrp'), function () {
            let id = $('#filter_by_room').find(':selected').data('id');
            if ($(this).data('id') == id) {
                $(this).removeClass('hidden').fadeIn(300);
            } else {
                $(this).addClass('hidden').fadeOut(300);
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
        margin: 15,
        loop: false,
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            600: {
                items: 2,
                nav: false
            },
            1200: {
                items: 3,
                nav: true
            },
            1980: {
                items: 4,
                nav: true
            }
        }
    });

    let y = new Intl.DateTimeFormat('en', {year: '2-digit'}).format(Date.now());
    let m = new Intl.DateTimeFormat('en', {month: '2-digit'}).format(Date.now());
    let d = new Intl.DateTimeFormat('en', {day: '2-digit'}).format(Date.now());

    $.each($('.room_wrp:not(.hidden) .owl-item'), function (index, val) {
        if ($(this).find('.item').data('date') == y + m + d) {
            carousel.trigger('to.owl.carousel', index);
        } else {
            carousel.trigger('to.owl.carousel', 0);
        }
    });

    function openModal(args, product, price, state) {

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
            '<div class="col-lg-4 col-12 order-lg-1 order-2 modal-sidebar">' +
            '<div class="img-holder owl-carousel owl-theme"> ' +
            '</div>' +
            '<h3 class="room-title"></h3>' +
            '<p class="room-description"></p>' +
            '<div class="trainer-title-wrp">' +
            '<span class="trainer-img-wrp>"></span>' +
            '<h3 class="room-trainer"></h3>' +
            '</div> ' +
            '<p class="trainer-description"></p>' +
            '<h5 class="event-duration"></h5>' +
            '</div>' +
            '<div class="col-lg-8 col-12 place-take d-flex align-content-center justify-content-center  order-lg-2 order-1">' +
            '<div class="trainer-place">' +
            '</div>' +
            '<div id="holder" class="variant-place-selector my-2 w-100 d-flex align-items-center justify-content-center">' +
            '<ul id="places" class="p-2">' +
            '</ul>' +
            '</div>' +
            '<div class="customer-note-wrp">' +
            '<h4>Order Note</h4>' +
            '<textarea id="customer_note" class="form-control mb-4"  maxlength="120" >' +
            '</textarea>' +
            '</div>' +
            '<div class="button-wrp  mb-4">' +
            '<span class="price mr-4"></span> ' +
            '<button type="button" id="submit_event" class="btn btn-primary">Book Place</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        setModalContent(html);


        $(dialogModal).modal('show');

        let rows, cols;
        let pool = [], pool_all = [];

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
                    //console.log(out);

                    $.each(fit.rooms, function (index, val) {
                        if (val.ID == out.data.room_id) {
                            $(dialogModal).find('.room-title').html(val.post_title);
                            $(dialogModal).find('.room-description').html(val.post_content);
                            let img = '';
                            $.each(val.room_gallery, function (index, val) {
                                img += '<div class="item">';
                                img += '<img  src="' + val + '"  class="room-image" >';
                                img += '</div>';
                            })

                            $(dialogModal).find('.img-holder').html(img);
                            $(dialogModal).find('.img-holder').owlCarousel({
                                items: 1,
                                lazyLoad: true,
                                navigation: false,
                                dots: false,
                                loop: true,
                                margin: 0
                            });
                        }
                    });


                    $.each(fit.trainers, function (index, val) {
                        if (val.ID == out.data.trainer_id) {
                            $(dialogModal).find('.room-trainer').html(val.post_title);
                            $(dialogModal).find('.trainer-place').after('<span class="room-trainer">' + val.post_title + '</span>');
                            let img = '';
                            $.each(val.trainer_photos, function (index, val) {
                                img += '<img src="' + val + '"  class="trainer-photo" >';
                            })
                            $(dialogModal).find('.trainer-description').html(val.post_content);
                            $(dialogModal).find('.trainer-place').html(img);
                        }
                    });

                    $(dialogModal).find('.modal-title').html(out.data.title);

                    let start_h = new Intl.DateTimeFormat('en-AU', {
                        hour: '2-digit',
                        hour12: false
                    }).format(new Date(out.data.start));

                    let start_m = new Intl.DateTimeFormat('en-AU', {
                        minute: '2-digit',
                        hour12: false,
                    }).format(new Date(out.data.start));

                    let end_h = new Intl.DateTimeFormat('en-AU', {
                        hour: '2-digit',
                        hour12: false
                    }).format(new Date(out.data.end));

                    let end_m = new Intl.DateTimeFormat('en-AU', {
                        minute: '2-digit',
                        hour12: false,
                    }).format(new Date(out.data.end));

                    $(dialogModal).find('.modal-title').append(' <span>(' + start_h + ':' + start_m + ' - ' + end_h + ':' + end_m + ')' + '</span>');

                    let settings = {
                        rowCssPrefix: 'row-',
                        colCssPrefix: 'col-',
                        placeWidth: 40,
                        placeHeight: 40,
                    };


                    $.each(fit.rooms, function (index, val) {
                        if (val.ID == out.data.room_id) {
                            cols = val.pool_capacity[0];
                            rows = val.pool_capacity[1];
                        }
                    });

                    let ResizeFunction = function () {

                        if (window.innerWidth >= 1200) {
                            settings.placeWidth = 45;
                            settings.placeHeight = 45;
                        }

                    }


                    $(dialogModal).find('#places').css('width', rows * settings.placeWidth + 20 + 'px');
                    $(dialogModal).find('#places').css('height', cols * settings.placeHeight + 20 + 'px');

                    ResizeFunction();
                    $(window).resize(ResizeFunction).trigger('resize');
                    let str = [];
                    let reservedPlaces = out.data.places_pool.split(',');
                    //console.log(reservedPlaces);
                    for (let i = 0; i < rows; i++) {
                        for (let j = 0; j < cols; j++) {
                            let placeNo = (i + j * rows + 1);
                            let className = 'place' + ' ' + settings.rowCssPrefix + i.toString() + ' ' + settings.colCssPrefix + j.toString();
                            if ($.inArray(placeNo + '', reservedPlaces) !== -1) {
                                className += ' selectedPlace';
                            }
                            str.push('<li class="' + className + '"' +
                                'style="top:' + (j * settings.placeHeight).toString() + 'px;left:' + (i * settings.placeWidth).toString() + 'px">' +
                                '<a title="' + placeNo + '">' + placeNo + '</a>' +
                                '</li>');
                        }
                    }

                    $(dialogModal).find('#places').html(str.join(''));

                    //let price = $('.room_wrp').data('price');
                    $(dialogModal).find('.price').html('Price: <span>' + fit.crns.replace('{{amount}}', price) + '</span>');
                    let price_ = price;
                    $(dialogModal).find('#places').on('click', '.place', function () {
                        if ($(this).hasClass('selectedPlace')) {
                            alert('This place is already reserved');
                        } else {
                            $(this).toggleClass('selectingPlace');
                            let price = price_ * $('#places li.selectingPlace').length;
                            $(dialogModal).find('.price').html('Price: ' + fit.crns.replace('{{amount}}', price));
                        }
                    });

                    $('#submit_event').on('click', function () {
                        let pool = [], pool_all = [];

                        // let product = $('.room_wrp').data('product');
                        let note = $('#customer_note').val().trim();
                        let room = $(dialogModal).find('.room-title').text().trim();
                        let trainer = $(dialogModal).find('.place-take .room-trainer').text().trim();
                        //let booked_places = out.data.places_pool;
                        let title = out.data.title;
                        let time = out.data.start;
                        $.each($('#places li.selectingPlace a'), function (index, value) {
                            pool.push($(this).attr('title'));
                        });
                        $.each($('#places li.selectingPlace a, #places li.selectedPlace a'), function (index, value) {
                            pool_all.push($(this).attr('title'));
                        });
                        let qty = pool.length;

                        pool = pool.length > 1 ? pool.join(',') : pool[0];
                        let permalink = 'https://' + $('.room_wrp').data('shop') + '/cart/' + product + ':' + qty +
                            '?attributes["id"]=' + out.data.id +
                            '&attributes["Event_Title"]=' + title +
                            '&attributes["Room"]=' + room +
                            '&attributes["Trainer"]=' + trainer +
                            '&attributes["Date_Time"]=' + time +
                            '&attributes["Select_Place"]=' + pool +
                            '&note=' + note;
                        if (qty > 0) {
                            //window.location.href = permalink;
                             window.open(permalink, '_blank');
                        } else {
                            alert('Select Place');
                        }

                    });


                },
                error: function (err) {
                    console.log(err.split(','));
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
        modal.setAttribute('data-backdrop', 'false');
        modal.innerHTML =
            '<div class="modal-dialog modal-xl book-dialog" role="document">' +
            '<div class="modal-content book-content"></div>' +
            '</div>';
        document.body.appendChild(modal);
        return modal;
    }

    /*--- booking place selector ---*/

});