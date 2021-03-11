document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');

    function calendar_render(m_room_id) {
        let calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            initialView: 'timeGridWeek',
            allDaySlot: false,
            height: 700,
            aspectRatio: 1,
            slotDuration: '00:15:00',
            slotLabelInterval: 15,
            eventTimeFormat: { // like '14:30:00'
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            slotLabelFormat: [
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            ],
            businessHours: [ // specify an array instead
                {
                    daysOfWeek: [1, 2, 3, 4, 5, 6, 7],
                    startTime: '06:00',
                    endTime: '22:00'
                }
            ],
            slotMinTime: "06:00",
            slotMaxTime: "22:00",
            scrollTime: "06:00",
            firstDay: 1,
            initialDate: Date.now(),
            editable: true,
            selectable: true,
            selectHelper: true,
            nowIndicator: true,
            dayMaxEvents: false, // allow "more" link when too many events
            eventOverlap: false,
            views: {
                month: {
                    select: true,
                    selectOverlap: false
                }
            },
            eventClick: function (args) {
                openModal(args, 'edit');
                jQuery('#dialogModal').modal('toggle');

                jQuery('#event_remove').on('click', function () {

                    let eData = {
                        id: args.event.id,
                    };
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'table_actions',
                            flag: 'del',
                            nonce: fit.nonce,
                            ti_data: eData
                        },
                        success: function (out) {
                            calendar.getEventById(args.event.id).remove();
                            calendar.refetchEvents();
                            //jQuery('#dialogModal').modal('toggle');
                        },
                        error: function (err) {
                            alert(err);
                        }
                    });
                });

                jQuery('#submit_event').on('click', function () {
                    let event = calendar.getEventById(args.event.id);
                    let title = jQuery('#event_title').val();
                    let room_id = jQuery('#room_edit').val();
                    let trainer_id = jQuery('#trainer_edit').find(':selected').data('id');
                    let product_id = jQuery('#product_edit').find(':selected').data('id');
                    let pool = [], item;
                    if (event.extendedProps.places_pool != '') {
                        pool.push(event.extendedProps.places_pool);
                    }

                    let pool_hide = 0;
                    if (jQuery('#hide_places').prop("checked")) {
                        pool_hide = 1;
                        console.log(pool_hide);
                    }

                    let clone_to = jQuery('#clone_to').val();

                    console.log(clone_to);

                    if (clone_to && title !== '') {
                        let clone_start = new Date(args.event.start);
                        let clone_end = new Date(args.event.end);
                        clone_to = new Date(clone_to);


                        clone_start.setDate(clone_start.getDate() + 7);
                        clone_end.setDate(clone_end.getDate() + 7);

                        do {
                            let pool = [], item;
                            jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                                item = jQuery(this).attr('title');
                                pool.push(item);
                            });

                            let eData = {
                                //id: args.event.id,
                                title: title !== '' ? title : args.event.title,
                                room_id: room_id !== '' ? room_id : args.event.room_id,
                                trainer_id: trainer_id,
                                product_id: product_id,
                                places_pool: pool.join(','),
                                pool_hide: pool_hide,
                                start: clone_start.toLocaleString('uk-UA'),
                                end: clone_end.toLocaleString('uk-UA'),
                            };


                            jQuery.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                cache: false,
                                data: {
                                    action: 'table_actions',
                                    flag: 'add',
                                    nonce: fit.nonce,
                                    ti_data: eData
                                },
                                success: function (out) {
                                    console.log(clone_start);
                                },
                                error: function (err) {
                                    console.log('error');
                                    console.table(err);
                                }
                            });

                            clone_start.setDate(clone_start.getDate() + 7);
                            clone_end.setDate(clone_end.getDate() + 7);

                        }
                        while (new Date(clone_start) <= new Date(clone_to));

                        calendar.unselect();
                        calendar.refetchEvents();
                    }


                    if (title !== '') {

                        jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                            item = jQuery(this).attr('title');
                            console.log(item);
                            pool.push(item);
                            console.log(pool);
                        });

                        let eData = {
                            id: args.event.id,
                            title: title !== '' ? title : args.event.title,
                            room_id: room_id !== '' ? room_id : args.event.room_id,
                            trainer_id: trainer_id,
                            product_id: product_id,
                            places_pool: pool.join(','),
                            pool_hide: pool_hide,
                            start: args.event.start.toLocaleString('uk-UA'),
                            end: args.event.end.toLocaleString('uk-UA'),

                        };

                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            data: {
                                action: 'table_actions',
                                flag: 'update',
                                nonce: fit.nonce,
                                ti_data: eData
                            },
                            success: function (out) {
                                calendar.refetchEvents();
                                jQuery('#dialogModal').modal('toggle');
                            },
                            error: function (err) {
                                console.log('error');
                                console.table(err);
                            }
                        });
                    } else {
                        alert('Empty Title!');
                    }
                });


            },
            select: function (args) {
                openModal(args, 'add');
                jQuery('#submit_event').on('click', function () {
                    let title = jQuery('#event_title').val();
                    let room_id = jQuery('#select_room_0').find(':selected').data('id');
                    let trainer_id = jQuery('#trainer_edit').find(':selected').data('id');
                    let product_id = jQuery('#product_edit').find(':selected').data('id');
                    let pool_hide = 0;
                    let clone_to = jQuery('#clone_to').val();

                    if (clone_to && title !== '') {
                        let clone_start = new Date(args.start);
                        let clone_end = new Date(args.end);
                        clone_to = new Date(clone_to);

                        clone_start.setDate(clone_start.getDate() + 7);
                        clone_end.setDate(clone_end.getDate() + 7);

                        do {

                            let pool = [], item;
                            jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                                item = jQuery(this).attr('title');
                                pool.push(item);
                            });

                            let eData = {
                                start: clone_start.toLocaleString('uk-UA'),
                                end: clone_end.toLocaleString('uk-UA'),
                                title: title,
                                room_id: room_id,
                                trainer_id: trainer_id,
                                product_id: product_id,
                                places_pool: pool.length < 2 ? pool : pool.join(','),
                                pool_hide: pool_hide
                            };

                            console.log('Fetching data:');
                            console.log(eData);

                            jQuery.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                cache: false,
                                data: {
                                    action: 'table_actions',
                                    flag: 'add',
                                    nonce: fit.nonce,
                                    ti_data: eData
                                },
                                success: function (out) {


                                },
                                error: function (err) {
                                    console.log('error');
                                    console.table(err);
                                }
                            });


                            clone_start.setDate(clone_start.getDate() + 7);
                            clone_end.setDate(clone_end.getDate() + 7);

                        }
                        while (new Date(clone_start) <= new Date(clone_to));
                        calendar.unselect();
                        calendar.refetchEvents();
                    }


                    if (jQuery('#hide_places').prop("checked")) {
                        pool_hide = 1;
                        console.log(pool_hide);
                    }

                    console.log(product_id);
                    if (title !== '') {
                        let pool = [], item;
                        jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                            item = jQuery(this).attr('title');
                            pool.push(item);
                        });

                        let eData = {
                            start: args.start.toLocaleString('uk-UA'),
                            end: args.end.toLocaleString('uk-UA'),
                            title: title,
                            room_id: room_id,
                            trainer_id: trainer_id,
                            product_id: product_id,
                            places_pool: pool.length < 2 ? pool : pool.join(','),
                            pool_hide: pool_hide
                        };

                        console.log('Fetching data:');
                        console.log(eData);

                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            data: {
                                action: 'table_actions',
                                flag: 'add',
                                nonce: fit.nonce,
                                ti_data: eData
                            },
                            success: function (out) {
                                calendar.unselect();
                                calendar.refetchEvents();
                                jQuery('#dialogModal').modal('toggle');

                                console.log('success');
                                console.log(out);
                            },
                            error: function (err) {
                                console.log('error');
                                console.table(err);
                            }
                        });
                    } else {
                        alert('Empty Title!');
                    }
                })
            },
            eventResize: function (args) {
                let eData = {
                    start: args.event.start.toLocaleString('uk-UA'),
                    end: args.event.end.toLocaleString('uk-UA'),
                    id: args.event.id,
                };
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'table_actions',
                        flag: 'update',
                        nonce: fit.nonce,
                        ti_data: eData
                    },
                    success: function (out) {
                        calendar.refetchEvents();
                    },
                    error: function (err) {
                        alert(err);
                    }
                });
            },
            eventDrop: function (args) {
                let eData = {
                    start: args.event.start.toLocaleString('uk-UA'),
                    end: args.event.end.toLocaleString('uk-UA'),
                    id: args.event.id,
                };
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'table_actions',
                        flag: 'update',
                        nonce: fit.nonce,
                        ti_data: eData
                    },
                    success: function (out) {
                        calendar.refetchEvents();
                    },
                    error: function (err) {
                        alert(err.split(","));
                    }
                });
            },

            eventContent: function (args) {
                let trainer_obj = document.createElement('i');
                let title_obj = document.createElement('strong');
                let places_obj = document.createElement('strong');

                let trainer = args.event.extendedProps.trainer_id;
                let places_ids = args.event.extendedProps.places_pool;

                title_obj.innerHTML = args.event.title + ' <small>' + args.timeText + '</small>';

                if (trainer) {
                    jQuery.each(fit.trainers, function (index, val) {
                        if (val.ID == trainer) {
                            trainer_obj.innerHTML = ' ' + val.post_title;
                        }
                    });
                }

                if (places_ids) {
                    places_obj.innerHTML = ' ' + places_ids;
                }
                let arrayOfDomNodes = [title_obj, trainer_obj, places_obj]
                return {domNodes: arrayOfDomNodes}
            },

            loading: function (bool) {
                //alert('events are being rendered'); // Add your script to show loading


                if (bool) {
                    jQuery('#calendar').addClass('disable-calendar');
                    console.log('Calendar reloaded');
                } else {
                    jQuery('#calendar').removeClass('disable-calendar');
                }
            },
            eventSources: [
                {
                    url: ajaxurl,
                    method: 'POST',
                    extraParams: {
                        nonce: fit.nonce,
                        action: 'table_actions',
                        room_id: m_room_id
                    },
                    eventOverlap: false,
                    error: function () {
                        alert('there was an error while fetching events!');
                    }
                }
            ]
        });

        calendar.render();

        jQuery('.nav-calendar').on('click', function () {
            calendar.refetchEvents();
        });

        function openModal(args, state) {

            let dialogModal = getModal();

            // Init the modal if it hasn't been already.
            if (!dialogModal) {
                dialogModal = initModal();
            }


            switch (state) {
                case 'add':
                    event_title = '';
                    event_start = args.start.toLocaleString('uk-UA');
                    event_end = args.end.toLocaleString('uk-UA');
                    remove = '';
                    poll_hide = '';
                    select_room = '';
                    pool = [];
                    pool_capacity = jQuery('#select_room_0').find(':selected').data('capacity').split(',');

                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'get_options',
                            nonce: fit.nonce,
                        },
                        success: function (out) {
                            let trainers = '';
                            let products = '';

                            jQuery.each(out.data['trainers'], function (index, val) {
                                trainers += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' > ' + val.post_title + '</option>'
                            });

                            jQuery.each(fit.products, function (index, val) {
                                products += '<option value="' + index + '" id="' + index + '" data-id="' + index + '" > ' + val + '</option>'
                            });

                            jQuery('#trainer_edit').html(trainers);
                            jQuery('#product_edit').html(products);

                        },
                        error: function (err) {
                            alert(err.data)
                        }
                    });
                    break;
                case 'edit':
                    let event = calendar.getEventById(args.event.id);
                    id = args.event.id;
                    event_title = args.event.title;
                    room_id = event.extendedProps.room_id;
                    trainer_id = event.extendedProps.trainer_id;
                    product_id = event.extendedProps.product_id;
                    pool = event.extendedProps.places_pool.split(',');
                    poll_hide = event.extendedProps.pool_hide == 1 ? 'checked' : '';


                    pool_capacity = jQuery('#select_room_0').find(':selected').data('capacity').split(',');
                    event_start = args.event.start.toLocaleString('uk-UA');
                    event_end = args.event.end.toLocaleString('uk-UA');
                    remove = '<button type="button" class="btn btn-danger" data-dismiss="modal" id="event_remove" data-id="' + id + '" >Remove</button>'
                    select_room = '<div class="form-group col"><label for="event_title">Event Gym</label><select name="room_edit" id="room_edit" value="' + room_id + '" class="form-control my-2"></select></div>';
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'get_options',
                            nonce: fit.nonce,
                        },
                        success: function (out) {
                            let rooms = '';
                            let trainers = '';
                            let products = '';
                            jQuery.each(out.data['rooms'], function (index, val) {
                                let selected = val.ID == room_id ? 'selected' : '';
                                rooms += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' ' + selected + '> ' + val.post_title + '</option>'
                            });

                            jQuery.each(out.data['trainers'], function (index, val) {
                                let selected = val.ID == trainer_id ? 'selected' : '';
                                trainers += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' ' + selected + '> ' + val.post_title + '</option>'
                            });

                            jQuery.each(fit.products, function (index, val) {
                                let selected = index == product_id ? 'selected' : '';
                                products += '<option value="' + index + '" id="' + index + '" data-id="' + index + '" ' + selected + '> ' + val + '</option>'
                            });

                            jQuery('#trainer_edit').html(trainers);
                            jQuery('#room_edit').html(rooms);
                            jQuery('#product_edit').html(products);

                        },
                        error: function (err) {
                            alert(err.data)
                        }
                    });

                    break;
            }


            let html =
                '<div class="modal-header">' +
                '<h5 class="modal-title" id="dialoglLabel">' + event_title + '<small class="text-info pl-2">' + event_start + ' -- ' + event_end + '</small></h5>' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="form-group">' +
                '<label for="event_title">Event title</label> ' +
                '<input type="text" name="event_title" id="event_title" value="' + event_title + '" class="form-control my-2" placeholder="Event Title" required>' +
                '</div>' +
                '<div class="row">' +
                select_room +
                '<div class="form-group col">' +
                '<label for="event_title">Event Trainer</label> ' +
                '<select name="trainer_edit" id="trainer_edit"  class="form-control my-2" required></select>' +
                '</div>' +
                '<div class="form-group col">' +
                '<label for="event_title">Attached Product</label> ' +
                '<select name="product_edit" id="product_edit"  class="form-control my-2" required></select>' +
                '</div>' +
                '</div>' +
                '<div id="holder" class="variant-place-selector my-2">' +
                '<ul id="places" class="p-2">' +
                placeInit(pool, pool_capacity[1], pool_capacity[0]) +
                '</ul>' +
                '</div>' +
                '<div class="row">' +
                '<div class="form-group repeat-group">' +
                '<label for="clone_to">Repeat to</label>' +
                '<input type="date" name="clone_to" id="clone_to" /> ' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<input type="checkbox" name="hide_places" id="hide_places"  /> <label for="hide_places" >Hide place selector</label>' +
                '<button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>' +
                '<button type="button" id="submit_event" class="btn btn-primary">Save changes</button>' +

                remove +
                '</div>';

            setModalContent(html);


            jQuery('#hide_places').on('change', function () {
                if (jQuery(this).prop("checked")) {
                    jQuery('.variant-place-selector').hide();
                } else {
                    jQuery('.variant-place-selector').show();
                }
            });

            jQuery('.place').on('click', function () {
                let pool_ = [];

                if (jQuery(this).hasClass('selectedPlace')) {
                    let id = jQuery(this).find('a').attr('title');
                    if (state == 'edit') {
                        let event = calendar.getEventById(args.event.id);
                        pool_ = removeItemAll(event.extendedProps.places_pool.split(','), id);
                    }
                    let eData = {
                        id: args.event.id,
                        places_pool: pool_.length < 2 ? pool_ : pool_.join(',')
                    };

                    console.log(eData);
                    let that = this;
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'table_actions',
                            flag: 'update_place',
                            nonce: fit.nonce,
                            ti_data: eData
                        },
                        success: function (out) {
                            jQuery(that).removeClass('selectedPlace');
                            let event = calendar.getEventById(args.event.id);
                            if (state == 'edit') {
                                event.setExtendedProp('places_pool', pool_);
                                calendar.refetchEvents();

                                console.log(event.extendedProps);
                            }
                        },
                        error: function (err) {
                            alert(err.data)
                        }
                    });

                } else {
                    jQuery(this).toggleClass('selectingPlace');
                }
            });

            // Show the modal.
            jQuery(dialogModal).modal('show');

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
                '<div class="modal-dialog" role="document">' +
                '<div class="modal-content"></div>' +
                '</div>';
            document.body.appendChild(modal);
            return modal;
        }
    }

    if (jQuery('#select_room_0').length > 0) {
        calendar_render(jQuery(this).find(':selected').data('id'));
        jQuery('#select_room_0').on('change', function () {
            calendar_render(jQuery(this).find(':selected').data('id'));
        });
    }

    /*--- booking place selector ---*/

    let settings = {
        rowCssPrefix: 'row-',
        colCssPrefix: 'col-',
        placeWidth: 43,
        placeHeight: 43,
    };

    function placeInit(reservedPlaces, rows, cols) {

        jQuery('#places').css('height', cols * settings.placeHeight);
        let str = [];
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                let placeNo = (i + j * rows + 1);
                let className = 'place' + ' ' + settings.rowCssPrefix + i.toString() + ' ' + settings.colCssPrefix + j.toString();
                if (jQuery.inArray(placeNo + '', reservedPlaces) !== -1) {
                    className += ' selectedPlace';
                }
                str.push('<li class="' + className + '"' +
                    'style="top:' + (j * settings.placeHeight).toString() + 'px;left:' + (i * settings.placeWidth).toString() + 'px">' +
                    '<a title="' + placeNo + '">' + placeNo + '</a>' +
                    '</li>');
            }
        }
        let html = str.join('');
        return html;
    };

    function removeItemAll(arr, value) {
        var i = 0;
        while (i < arr.length) {
            if (arr[i] === value) {
                arr.splice(i, 1);
            } else {
                ++i;
            }
        }

        return arr.filter(function (el) {
            return el != '';
        });
    }


    /*-----------------*/
    let groupColumn = 1;
    let table = jQuery('#event_orders').DataTable({

        ajax: {
            url: ajaxurl + '?action=get_orders'
        },
        responsive: true,
        stateSave: true,
        columns: [
            {data: 'event'},
            {data: 'event_room'},
            {
                data: 'order_id',
                render: function (data, type) {
                    // https://repose-space.myshopify.com/admin/orders/2957335461923?orderListBeta=true
                    if (type === 'display' && data !== null) {
                        return '<a href="https://repose-space.myshopify.com/admin/orders/' + data + '?orderListBeta=true" target="_blank" >' + data + '</a> ';
                    } else {
                        return data;
                    }

                }

            },
            {data: 'name'},
            {data: 'surname'},
            {
                className: 'order-email',
                data: 'email',
                render: function (data, type) {
                    if (type === 'display' && data !== null) {
                        return '<a href="mailto: ' + data + '" >' + data + '</a> ';
                    } else {
                        return data;
                    }
                }

            },
            {
                className: 'order-phone',
                data: 'phone',
                render: function (data, type) {
                    if (type === 'display' && data !== null) {
                        return '<a href="tel : ' + data + '" >' + data + '</a> ';
                    } else {
                        return data;
                    }
                }

            },
            {data: 'place'},
            {data: 'note'},
            {
                className: 'order-status',
                data: 'status'
            },
        ],

        columnDefs: [
            {
                "targets": [0],
                "visible": false,
                "searchable": true
            },
            {
                "targets": [1],
                "visible": false,
                "searchable": true
            },
        ],
        rowGroup: {
            dataSrc: ['event_room', 'event']
        }
    });


    // Order by the grouping
    jQuery('#event_orders tbody').on('click', 'tr.group', function () {
        let currentOrder = table.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            table.order([groupColumn, 'desc']).draw();
        } else {
            table.order([groupColumn, 'asc']).draw();
        }
    });

    setInterval(function () {
        table.ajax.reload(null, false); // user paging is not reset on reload
    }, 10000);

});

