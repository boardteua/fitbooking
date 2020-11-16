document.addEventListener('DOMContentLoaded', function () {


        let calendarEl = document.getElementById('calendar');
        //filterRooms();

        //filterRooms();
        //calendar.refetchEvents();
        function calendar_render(m_room_id) {
            let calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                initialView: 'timeGridWeek',
                timeZone: 'local',
                allDaySlot: false,
                height: 700,
                aspectRatio: 1,
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
                        daysOfWeek: [1, 2, 3, 4, 5],
                        startTime: '09:00',
                        endTime: '19:00'
                    },
                    {
                        daysOfWeek: [6, 7],
                        startTime: '10:00',
                        endTime: '16:00'
                    }
                ],
                slotMinTime: "09:00",
                slotMaxTime: "18:00",
                scrollTime: "09:00",
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
                                console.table(out);
                                calendar.getEventById(args.event.id).remove();
                                calendar.refetchEvents();
                                jQuery('#dialogModal').modal('toggle');
                            },
                            error: function (err) {
                                alert(err);
                            }
                        });
                    });

                    jQuery('#submit_event').on('click', function () {

                        let title = jQuery('#event_title').val();
                        let room_id = jQuery('#room_edit').val();
                        let trainer_id = jQuery('#trainer_edit').find(':selected').data('id');
                        let pool = args.event._def.extendedProps.places_pool.split(','), item;
                        jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                            item = jQuery(this).attr('title');
                            pool.push(item);
                        });

                        let eData = {
                            id: args.event.id,
                            title: title !== '' ? title : args.event.title,
                            room_id: room_id !== '' ? room_id : args.event.room_id,
                            trainer_id: trainer_id,
                            places_pool: pool.join(','),
                            start: args.event.start.toLocaleString(),
                            end: args.event.end.toLocaleString(),

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
                                console.table(out);
                                calendar.refetchEvents();
                                jQuery('#dialogModal').modal('toggle');
                            },
                            error: function (err) {
                                alert(err);
                            }
                        });
                    });


                },
                select: function (args) {
                    openModal(args, 'add');
                    jQuery('#submit_event').on('click', function () {
                        let title = jQuery('#event_title').val();
                        let room_id = jQuery('#select_room_0').find(':selected').data('id');
                        let trainer_id = jQuery('#trainer_edit').find(':selected').data('id');
                        console.log(room_id);
                        if (title !== '') {
                            let pool = [], item;
                            jQuery.each(jQuery('#places li.selectingPlace a'), function (index, value) {
                                item = jQuery(this).attr('title');
                                pool.push(item);
                            });

                            let eData = {
                                start: args.start.toLocaleString(),
                                end: args.end.toLocaleString(),
                                title: title,
                                room_id: room_id,
                                trainer_id: trainer_id,
                                places_pool: pool.join(','),
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
                                    console.table(out);
                                    calendar.refetchEvents();
                                    calendar.unselect();
                                    jQuery('#dialogModal').modal('toggle');
                                },
                                error: function (err) {
                                    alert(err);
                                }
                            });
                        }
                    })
                },
                eventResize: function (args) {
                    let eData = {
                        start: args.event.start.toLocaleString(),
                        end: args.event.end.toLocaleString(),
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
                            console.table(out);
                            calendar.refetchEvents();
                        },
                        error: function (err) {
                            alert(err);
                        }
                    });
                },
                eventDrop: function (args) {
                    console.table(args.event.start.toLocaleString());
                    let eData = {
                        start: args.event.start.toLocaleString(),
                        end: args.event.end.toLocaleString(),
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
                            console.table(out);
                            calendar.refetchEvents();
                        },
                        error: function (err) {
                            alert(err);
                        }
                    });
                },

                eventContent: function (args) {
                    let time_obj = document.createElement('strong');
                    let trainer_obj = document.createElement('i');
                    let trainer_id = args.event._def.extendedProps.trainer_id;

                    if (trainer_id) {
                        jQuery.each(fit.trainers, function (index, val) {
                            console.log(val);
                            if (val.ID == trainer_id) {
                                trainer_obj.innerHTML = 'Trainer: ' + val.post_title;
                            }
                        });
                    }
                    time_obj.innerHTML = args.timeText;
                    let arrayOfDomNodes = [time_obj, trainer_obj]
                    return {domNodes: arrayOfDomNodes}
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
                        timeZone: 'UTC',
                        eventOverlap: false,
                        error: function () {
                            alert('there was an error while fetching events!');
                        }
                    }
                ]
            });

            calendar.render();

            // jQuery('#select_room_0').on('change', function () {
            //     //filterRooms();            calendar.
            //     calendar.getEventSources();
            //     calendar_render($(this))
            // });

            function openModal(args, state) {

                let dialogModal = getModal();

                // Init the modal if it hasn't been already.
                if (!dialogModal) {
                    dialogModal = initModal();
                }

                switch (state) {
                    case 'add':
                        event_title = '';
                        event_start = args.start.toLocaleString();
                        event_end = args.end.toLocaleString();
                        remove = '';
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
                                jQuery.each(out.data['trainers'], function (index, val) {
                                    trainers += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' > ' + val.post_title + '</option>'
                                });
                                jQuery('#trainer_edit').html(trainers);
                            },
                            error: function (err) {
                                alert(err.data)
                            }
                        });
                        break;
                    case 'edit':
                        id = args.event.id;
                        event_title = args.event.title;
                        room_id = args.event._def.extendedProps.room_id;
                        trainer_id = args.event._def.extendedProps.trainer_id;
                        pool = args.event._def.extendedProps.places_pool.split(',');
                        pool_capacity = jQuery('#select_room_0').find(':selected').data('capacity').split(',');
                        event_start = args.event.start.toLocaleString();
                        event_end = args.event.end.toLocaleString();
                        remove = '<button type="button" class="btn btn-danger" data-dismiss="modal" id="event_remove" data-id="' + id + '" >Remove</button>'
                        select_room = '<select name="room_edit" id="room_edit" value="' + room_id + '" class="form-control my-2"></select>';
                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            data: {
                                action: 'get_options',
                                nonce: fit.nonce,
                            },
                            success: function (out) {
                                console.log(out.data);
                                let rooms = '';
                                let trainers = '';
                                jQuery.each(out.data['rooms'], function (index, val) {
                                    let selected = val.ID == room_id ? 'selected' : '';
                                    rooms += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' ' + selected + '> ' + val.post_title + '</option>'
                                });

                                jQuery.each(out.data['trainers'], function (index, val) {
                                    let selected = val.ID == trainer_id ? 'selected' : '';
                                    trainers += '<option value="' + val.ID + '" id="room-' + val.ID + '" data-id=' + val.ID + ' ' + selected + '> ' + val.post_title + '</option>'
                                });
                                jQuery('#trainer_edit').html(trainers);
                                jQuery('#room_edit').html(rooms);

                            },
                            error: function (err) {
                                alert(err.data)
                            }
                        });

                        break;
                }

                let html =
                    '<div class="modal-header">' +
                    '<h5 class="modal-title" id="dialoglLabel">' + event_title + '<small class="text-info font-size-xs pl-2">' + event_start + ' -- ' + event_end + '</small></h5>' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '<input type="text" name="event_title" id="event_title" value="' + event_title + '" class="form-control my-2" placeholder="Event Title" required>' +

                    select_room +
                    '<select name="trainer_edit" id="trainer_edit"  class="form-control my-2" required></select>' +
                    '<div id="holder" class="variant-place-selector my-2">' +
                    '<ul id="places" class="p-2">' +
                    placeInit(pool, pool_capacity[1], pool_capacity[0]) +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                    '<button type="button" id="submit_event" class="btn btn-primary">Save changes</button>' +
                    remove +
                    '</div>';

                setModalContent(html);

                jQuery('.place').on('click', function () {
                    if (jQuery(this).hasClass('selectedPlace')) {
                        alert('This place is already reserved');
                    } else {
                        jQuery(this).toggleClass('selectingPlace');

                    }
                });

                // $('#btnShow').click(function () {
                //     var str = [];
                //     $.each($('#places li.' + settings.selectedPlaceCss + ' a, #places li.' + settings.selectingPlaceCss + ' a'), function (index, value) {
                //         str.push($(this).attr('title'));
                //     });
                //     alert(str.join(','));
                // })
                //
                // $('#btnShowNew').click(function () {
                //     var str = [], item;
                //     $.each($('#places li.' + settings.selectingPlaceCss + ' a'), function (index, value) {
                //         item = $(this).attr('title');
                //         str.push(item);
                //     });
                //     alert(str.join(','));
                // });

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

            function filterRooms() {
                let room_id = jQuery('#select_room_0').find(':selected').data('id')

                calendar.batchRendering(function () {
                    let events = calendar.getEvents()

                    for (let i = 0; i < events.length; i++) {
                        let event = events[i]
                        if (event._def.extendedProps.room_id != room_id) {
                            console.log(room_id);
                            event.setProp('display', 'none');

                        } else {
                            event.setProp('display', 'block')
                        }
                    }
                });
                calendar.render();
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
            let str = [], placeNo, className;
            console.log(reservedPlaces);

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
            let html = str.join('');
            return html;
        };


        /*-----------------*/

    }
);

