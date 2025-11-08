$('.breadcumbs-header-custom').each(function () {
    $(this).find('ol').find('li:last-child').addClass('active');
});

$(document).on("keydown", ":input:not(textarea)", function (event) {
    if (event.key == "Enter") {
        event.preventDefault();
    }
});

$(document).on('keyup change paste', 'form input, form select, form textarea', function () {
    if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {
        let nameElement = $(this).attr('name');
        $(`[name="${nameElement}"]`).each(function () {
            $(this).removeClass('is-invalid');
        });
    } else {
        $(this).removeClass("is-invalid");
    }
    
    //special for flatpickr
    if ($(this).hasClass('flatpickr')) {
        $(this).parents('.form-group').find('.flatpickr').removeClass('is-invalid');
    }
    
    $(this).parents(".form-group").find(".invalid-feedback").html(null).removeClass("d-block");
});

$(document).ready(function () {
    initAllComponents();
});

function initAllComponents() {
    initSelect2();
    initPopOver();
    initTooltips();
    // initFormRepeater();
    // initDataFilter();
    // initSubmitButton();
    initFlatpickr();
}

function initSubmitButton() {
    $('button.submit').each(function () {
        $(this).prop('disabled', false);
    }
    );
}

function initFlatpickr() {
    $('.flatpickr').each(function () {
        const dataMinDate = $(this).attr('data-min-date') ? new Date($(this).attr('data-min-date')) : new Date();
        const dataMaxDate = $(this).attr('data-max-date') ? new Date($(this).attr('data-max-date')) : new Date();
        attrMinOffset = $(this).attr('data-min-offset') ? dataMinDate.fp_incr($(this).attr('data-min-offset')) : null;
        attrMaxOffset = $(this).attr('data-max-offset') ? dataMaxDate.fp_incr($(this).attr('data-max-offset')) : null;

        if($(this).hasClass('date-time')) {
            $(this).flatpickr({
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'j F Y H:i',
                minDate: attrMinOffset,
                maxDate: attrMaxOffset
            });
        } else if ($(this).hasClass('time')) {
            $(this).flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                altInput: true,
                altFormat: 'H:i',
            });
        } else {
            $(this).flatpickr({
                altInput: true,
                altFormat: 'j F Y',
                dateFormat: 'Y-m-d',
                minDate: attrMinOffset,
                maxDate: attrMaxOffset
            });
        }
    });
}
    

function initSelect2(element = null, data = null) {
    let options = 
        {
            tags: $(element).attr('data-tags') === 'true' ?? false,
            allowClear: true,
            placeholder: $(element).attr('data-placeholder') ?? null,
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $(element).parent(),
        }
    ;

    if(data != null) {
        options['data'] = data;
    }

    if (element != null) {
        if ($(element).hasClass("select2-hidden-accessible")) {
            $(element).select2("destroy");
        }
        $(element).select2(options);
        return;
    }

    $('select.select2').each(function () {
        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $(this).removeAttr('data-select2-id tabindex aria-hidden');
            $(this).parent().removeAttr('data-select2-id');
        }

        if (!$(this).parent().hasClass('position-relative')) $(this).wrap('<div class="position-relative w-100"></div>');

        $(this).select2({
            tags: $(this).attr('data-tags') === 'true' ?? false,
            allowClear: $(this).attr('data-allow-clear') === 'true' ?? false,
            placeholder: $(this).attr('data-placeholder') ?? null,
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $(this).parent(),
        });
    });
}

function initPopOver() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// function initFormRepeater(el = $('.form-repeater')) {
//     var row = 2;
//     var col = 1;
//     let formRepeater = el;
//     if (formRepeater.length == 0) return;

//     let dataCallbackForm = formRepeater.attr('data-callback');

//     formRepeater.repeater({
//         show: function () {
//             let dataCallback = $(this).attr('data-callback');
//             if (typeof window[dataCallback] === "function") window[dataCallback](this);

//             var fromControl = $(this).find('.form-control, .form-select, .form-check-input');
//             var formLabel = $(this).find('.form-label, .form-check-label');

//             fromControl.each(function (i) {
//                 if (!$(this).hasClass('flatpickr')) {
//                     var id = 'form-repeater-' + row + '-' + col;
//                     $(fromControl[i]).attr('id', id);
//                     $(formLabel[i]).attr('for', id);
//                     col++;
//                 }
//             });

//             row++;

//             // fix select2
//             initSelect2();
//             initFlatpickr();
//             // --------------------------------------------

//             $(this).slideDown();
//         },
//         hide: function (e) {
//             let confirm_ = false;
//             sweetAlertConfirm({
//                 title: 'Are you sure?',
//                 text: 'You are about to delete this element!',
//                 icon: 'warning',
//                 confirmButtonText: 'Yes, I do!',
//                 cancelButtonText: 'No, cancel!',
//             }, () => {
//                 confirm_ = true;
//                 if (!confirm_) return;
//                 let dataCallback = $(this).attr('data-callback');
//                 if (typeof window[dataCallback] === "function") window[dataCallback](this);
                
//                 $(this).slideUp(e);
//             });
//         },
//         isFirstItemUndeletable: true
//     });

//     if (typeof window[dataCallbackForm] === "function") window[dataCallbackForm](formRepeater);

//     return formRepeater;
// }

function btnBlock(element, bool = true) {
    if (bool) {
        element.prop('disabled', true);
        element.block({
            message: '<div class="spinner-border text-light" role="status"></div>',
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
            },
            overlayCSS: {
                opacity: 0.5
            }
        });
    } else {
        element.prop('disabled', false);
        element.unblock();
    }
}

/***
 * Function to block section
 * @param element
 * @param bool
 * @returns {void}
 * */
function sectionBlock(element, bool = true) {
    if (bool) {
        element.block({
            message: '<div class="spinner-border text-light" role="status"></div>',
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
            },
            overlayCSS: {
                opacity: 0.5,
                backgroundColor: '#fff'
            }
        });
    } else {
        element.unblock();
    }
}

/***
 * Function to block page
 * @param bool
 * @returns {void}
 * */
function pageBlock(bool = true) {
    if (bool) {
        $.blockUI({
            message: '<div class="d-flex justify-content-center"><p class="mb-0">Sedang memproses...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
            css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
            },
            overlayCSS: {
                opacity: 0.5
            }
        });
    } else {
        $.unblockUI();
    }
}

/***
 * Function to show sweet alert confirm
 * @param config
 * @param callback
 * @returns {Promise<void>}
 * */
function sweetAlertConfirm(config, callback) {
    let title, text, icon, confirmButtonText, cancelButtonText;

    title = config.title ?? 'Are you sure?';
    text = config.text ?? "You won't be able to revert this!";
    icon = config.icon ?? 'warning';
    confirmButtonText = config.confirmButtonText ?? 'Yes, I do!';
    cancelButtonText = config.cancelButtonText ?? 'No, cancel!';
    reverseButton = config.reverseButton ?? false;
    customClassConfirm = config.customClassConfirm ?? 'btn btn-primary me-3';
    customClassCancel = config.customClassCancel ?? 'btn btn-label-secondary';

    return Swal.fire({
        html: '<h3>' + title + '</h3><p>' + text + '</p>',
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        customClass: {
            confirmButton: customClassConfirm,
            cancelButton: customClassCancel
        },
        buttonsStyling: false,
        reverseButtons: reverseButton
    }).then(function (result) {
        if (result.isConfirmed) {
            if (typeof callback === "function") callback();
        }
    });
}

/***
 * Function to show sweet alert
 * @param config
 * @returns {Promise<void>}
 * */
function showSweetAlert(config) {
    let title, text, icon;

    title = config.title ?? '';
    text = config.text ?? 'The action was executed successfully.';
    icon = config.icon ?? 'success';
    showConfirmButton = config.showConfirmButton ?? true
    confirmButtonText = config.confirmButtonText ?? 'OK';
    showCancelButton = config.showCancelButton ?? false
    cancelButtonText = config.cancelButtonText ?? 'Batal';

    customClass = {
        confirmButton: 'btn btn-primary',
    }

    if(showCancelButton) {
        customClass['cancelButton'] = 'btn btn-outline-danger';
    }

    return Swal.fire({
        html: '<h3>' + title + '</h3><p>' + text + '</p>',
        icon: icon,
        showConfirmButton: showConfirmButton,
        confirmButtonText: confirmButtonText,
        showCancelButton: showCancelButton,
        cancelButtonText: cancelButtonText,
        customClass: customClass,
    });
}

// $(document).on("submit", ".default-form", function (event) {
//     event.preventDefault();
//     var button = $(this).find(":submit");
//     btnBlock(button);
//     store_data(this, button);
// });

/***
 * Function to store data
 * @param content
 * @param button
 * @returns {void}
 * */
// function store_data(content, button) {
//     $("input").blur();

//     $(content).find('.is-invalid').removeClass('is-invalid');
//     $(content).find('.invalid-feedback').html(null).removeClass('d-block');

//     let form_data = new FormData(content);
//     let action = $(content).attr("action");
//     let callback = $(content).attr("function-callback") ?? null;

//     $.ajax({
//         url: action,
//         type: "POST",
//         data: form_data,
//         processData: false, // tell jQuery not to process the data
//         contentType: false, // tell jQuery not to set contentType
//         cache: false,
//         success: function (response) {
//             btnBlock(button, false);
//             if (!response.error) {                
//                 if (
//                     (response.data == null) ||
//                     (response.data != null && !response.data.ignore_alert)
//                 ) {
//                     showSweetAlert({
//                         title: response?.data?.title ?? 'Berhasil!',
//                         text: response.message,
//                         icon: response?.data?.icon ?? 'success',
//                         showConfirmButton: response?.data?.showConfirmButton
//                     });                   
//                 }

//                 if (typeof window[callback] === "function") window[callback](response);
//             } else {
//                 showSweetAlert({
//                     title: 'Failed!',
//                     text: response.message,
//                     icon: 'error'
//                 });
//             }
//         },
//         error: (xhr, status, error) => {
//             btnBlock(button, false);
//             let res = xhr.responseJSON;
//             if (res.errors) {
//                 $.each(res.errors, function (key, value) {
//                     key = errorFormater(key);

//                     //find closest flatpickr input and add invalid
//                     if ($(`[name="${key}"]`).hasClass('flatpickr')) {
//                         $(`[name="${key}"]`).parents('.form-group').find('.flatpickr').addClass('is-invalid');
//                     }

//                     if ($(`[name="${key}"]`, content).length > 0) {
//                         $(`[name="${key}"]`, content).addClass('is-invalid');
//                         $(`[name="${key}"]`, content).parents('.form-group').find('.invalid-feedback').html(value[0]).addClass('d-block');
//                         $('html, body').scrollTop($(`[name="${key}"]`).offset().top-100);
//                     } else {
//                         $(`[name^="${key}"]`, content).addClass('is-invalid');
//                         $(`[name^="${key}"]`, content).parents('.form-group').find('.invalid-feedback').html(value[0]).addClass('d-block');
//                         $('html, body').scrollTop($(`[name^="${key}"]`).offset().top-100);
//                     }
//                 });
//             } else {
//                 showSweetAlert({
//                     title: 'Failed!',
//                     text: res.message,
//                     icon: 'error'
//                 });
//             }
//         },
//     }).always(function () {
//         btnBlock(button, false);
//     });
// }

// response formater

function errorFormater(keyError) {
    const newKey = keyError.split('.').reduce((acc, segment, index) => {
        return index === 0 ? segment : `${acc}[${segment}]`;
      }, '');
      
    return newKey;
}

// Triggered on modal hide
$('.modal:not(.custom-reset)').on('hide.bs.modal', function () {
    let form = $(this).find('form');

    form.trigger('reset');
    form.find('.select2').val(null).trigger('change');
    form.find('.select2_custom').val(null).trigger('change');
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').html(null).removeClass('d-block');
    // form.find('[data-repeater-item]').slice(1).empty();
    form.find('[data-repeater-list]').each(function () {
        $(this).find('[data-repeater-item]').slice(1).empty();
    });
    form.find('.current-file').remove();

    // reset flatpickr
    // form.find('.flatpickr-date').val(null).trigger('change');
    // form.find('.flatpickr-date').flatpickr({
    //     altInput: true,
    //     altFormat: 'j F Y',
    //     dateFormat: 'Y-m-d'
    // });
});

// // Triggered every update status button
// $(document).on('click', '.update-status', function () {
//     let url = $(this).data('url');
//     let dataFunction = $(this).attr('data-function');

//     sweetAlertConfirm({
//         title: 'Are you sure?',
//         text: 'You are about to change the status of this data!',
//         icon: 'warning',
//         confirmButtonText: 'Yes, I do!',
//         cancelButtonText: 'No, cancel!',
//     }, function () {
//         $.ajax({
//             url: url,
//             type: "POST",
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             success: function (response) {
//                 if (!response.error) {
//                     showSweetAlert({
//                         title: response?.data?.title ?? 'Berhasil!',
//                         text: response.message,
//                         icon: response?.data?.icon ?? 'success',
//                         showConfirmButton: response?.data?.showConfirmButton
//                     });

//                     if (typeof window[dataFunction] === "function") window[dataFunction](response);
//                 } else {
//                     showSweetAlert({
//                         title: 'Failed!',
//                         text: response.message,
//                         icon: 'error'
//                     });
//                 }
//             },
//             error: function (xhr, status, error) {
//                 let res = xhr.responseJSON;
//                 showSweetAlert({
//                     title: 'Failed!',
//                     text: res.message,
//                     icon: 'error'
//                 });
//             }
//         });
//     });
// });

// /***
//  * Function to filter input value
//  * @returns {void}
//  */
// function initDataFilter(){
//     let inputs = document.querySelectorAll('input[data-filter]');
//     for (let input of inputs) {
//     let state = {
//         value: input.value,
//         start: input.selectionStart,
//         end: input.selectionEnd,
//         pattern: RegExp('^' + input.dataset.filter + '$')
//     };

//     input.addEventListener('input', event => {
//         if (state.pattern.test(input.value)) {
//         state.value = input.value;
//         } else {
//         input.value = state.value;
//         input.setSelectionRange(state.start, state.end);
//         }
//     });

//     input.addEventListener('keydown', event => {
//         state.start = input.selectionStart;
//         state.end = input.selectionEnd;
//     });
//     }
// }

/***
 * Function to show tooltip filter
 * @returns {void}
 */
function tooltip() {
  var filter = $("#filter_form")
    .serializeArray()
    .map(function (item) {
      let input = $("#filter_form").find(`[name="${item.name}"]`);
      var formattedName = input.parents(".col").find(".form-label").html();

      SelectedText = "";
      if (input.is('input[type="radio"]')) {
        selectedText = $('input[name="' + item.name + '"]:checked')
          .parent()
          .find(".form-check-label")
          .text();
      } else if (input.is("select")) {
        selectedText = input.find(":selected").text();
      } else {
        selectedText = input.val();
      }

      // Only return non-empty values
      return selectedText ? `${formattedName} : ${selectedText}` : null;
    })
    .filter(Boolean)
    .join(", ");
  $("#tooltip-filter").attr("data-bs-original-title", filter);
}

/***
 * Function to insert value to modal form
 * @param response
 * @param modal
 * @returns {void}
 */
// function insertEditValue(response, modal){
//     $.each(response, function(key, value) {
//         let element = modal.find(`#${key}`);
//         if (element.is('select') && element.find('option').length <= 1) {
//             let interval = setInterval(() => {
//                 if (element.children('option').length > 1) {
//                     element.val(value).trigger('change');
//                     clearInterval(interval);
//                 }
//             }, 10);
//         } else if (element.is('input[type="radio"]')) {
//             modal.find(`input[name="${key}"][value="${value}"]`).prop('checked', true);
//         } else if (element.is('input[type="checkbox"]')) {
//             let values = JSON.parse(value);
//             values.forEach(val => {
//                 modal.find(`[value="${val}"]`).prop('checked', true);
//             });
//         } else if (element.is('input[type="file"]')) {
//             if(value) {
//                 let link = `<a href="${value}" target="_blank" class="d-block mt-2 current-file"><small>Lihat Current File</small></a>`;
//                 element.after(link);
//             }
//         } else {
//             element.val(value).trigger('change');
//         }
//     });
// }

document.addEventListener('livewire:init', () => {
    Livewire.on('after-action', (event) => {
        const callbackName = event[0].callback;
        const payload = event[0]?.payload?.original || {};

        showSweetAlert({
            title: payload?.data?.title ?? 'Berhasil!',
            text: payload.message,
            icon: payload?.data?.icon ?? 'success',
            showConfirmButton: payload?.data?.showConfirmButton
        }); 

        if (typeof window[callbackName] === 'function') {
            window[callbackName](payload);
        }
        
    });

    Livewire.on('after-get-data', (event) => {
        const callbackName = event[0].callback;
        const payload = event[0].payload || {};

        if (typeof window[callbackName] === 'function') {
            window[callbackName](payload);
        }
    });

    Livewire.on('show-error', ({ message }) => {
        console.log(message);
    });

    Livewire.on('fail-validation', (payload) => {
        item = payload[0];
        const key = Object.keys(item);
        let input;
        let messages;

        key.forEach(k => {
            messages = item[k];
            // Cari elemen yang punya wire:model (atau .live / .blur / .debounce dll) dengan nilai key
            input = document.querySelector(
                `[wire\\:model="${k}"],
                [wire\\:model\\.live="${k}"],
                [wire\\:model\\.lazy="${k}"],
                [wire\\:model\\.blur="${k}"],
                [wire\\:model\\.debounce\\.500ms="${k}"],
                [wire\\:model\\.defer="${k}"]`
            );

            if (input) {
                // contoh: tambahkan border merah
                input.classList.add('is-invalid');
                $el = $(input);
                $el.addClass('is-invalid');

                $parentEl = $el.closest('.form-group');
                $parentEl.find('.invalid-feedback').addClass('d-block').html(messages[0]);
            } else {
                console.warn(`Elemen dengan wire:model=${k} tidak ditemukan`);
            }// Cari elemen yang punya wire:model (atau .live / .blur / .debounce dll) dengan nilai key
        });
    });
});