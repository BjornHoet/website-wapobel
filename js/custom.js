$(document).ready(function () {
	$("#inputCheckPassword").on("input", validation);
	$("#inputPasswordLogin").on("input", validation);

	document.querySelector('.bg-login-image')?.addEventListener('click', () => {
		window.location.href = "https://www.wapobel.be";
	}); 

	const input = document.querySelector('#inputPasswordLogin');
	const toggle = document.querySelector('.toggle-password');
	
	if (input) {
		// Toon/verberg oogje afhankelijk van lengte
		input.addEventListener('input', function () {
			if (this.value.length > 0) {
				toggle.style.display = 'block';
			} else {
				toggle.style.display = 'none';
			}
		});
	}

	// Toggle zichtbaar/onzichtbaar
	if (toggle) {
		toggle.addEventListener('click', function () {
			const type = input.type === 'password' ? 'text' : 'password';
			input.type = type;

			this.classList.toggle('fa-eye');
			this.classList.toggle('fa-eye-slash');
		});
	}

	const inputCheck = document.querySelector('#inputCheckPassword');
	const toggleCheck = document.querySelector('.toggle-passwordCheck');

	if (inputCheck) {
		// Toon/verberg oogje afhankelijk van lengte
		inputCheck.addEventListener('input', function () {
			if (this.value.length > 0) {
				toggleCheck.style.display = 'block';
			} else {
				toggleCheck.style.display = 'none';
			}
		});
	}

	// Toggle zichtbaar/onzichtbaar
	if (toggleCheck) {
		toggleCheck.addEventListener('click', function () {
			const type = inputCheck.type === 'password' ? 'text' : 'password';
			inputCheck.type = type;

			// Oog ↔ oogje-dicht wisselen
			this.classList.toggle('fa-eye');
			this.classList.toggle('fa-eye-slash');
		});
	}
});


document.addEventListener('DOMContentLoaded', () => {
    const billitToggle = document.getElementById('inputBillit');
    const showBillitToggle = document.getElementById('inputShowBillit');

    // Init: indien Billit uitstaat, showBillit ook uitzetten en disabled maken
    if (!billitToggle.checked) {
      showBillitToggle.checked = false;
      showBillitToggle.disabled = true;
    }

    // Event: wanneer Billit aan/uit wordt gezet
    billitToggle.addEventListener('change', () => {
      if (billitToggle.checked) {
        // indien aangevinkt, ShowBillit aanzetten en unlocken
        showBillitToggle.disabled = false;
      } else {
        // indien uitgevinkt, ShowBillit uitzetten en locken
        showBillitToggle.checked = false;
        showBillitToggle.disabled = true;
      }
    });
});
  
function valueChanged() {
    /* if($('#inputNummering').is(":checked"))   
        $("#inputSortering").show();
    else
        $("#inputSortering").hide();
		document.getElementById('selectSortering').value = '0'; */
    }

function billitValueChanged() {
    const enabled = $('#inputBillit').is(":checked");

    $("#inputBillitAPIKey")
        .prop('readonly', !enabled)
        .prop('required', enabled);
	}

function validation() {
    var password1 = document.getElementById("inputPasswordLogin").value;
    var password2 = document.getElementById("inputCheckPassword").value;
    var feedback = document.getElementById("passwordFeedback");
    var submitBtn = document.getElementById("profileSubmit");

    if (!feedback) return; // safety check

    /* ✅ NIEUW: beide velden leeg → volledige reset */
	if (password1.length === 0 && password2.length === 0) {
		feedback.classList.add("d-none");
		feedback.innerHTML = "";

		// ✅ reset naar standaard toestand
		submitBtn.disabled = false;

		return;
	}

    /* ✅ wachtwoorden correct */
    if (password1 === password2 && password1.length > 0) {
        feedback.className = "alert alert-success text-center font-weight-bold py-2 px-3";
        feedback.innerHTML =
            '<i class="fas fa-check mr-2"></i>Wachtwoorden komen overeen.<i class="fas fa-check ml-2"></i>';
        feedback.classList.remove("d-none");
        submitBtn.disabled = false;
    } 
    /* ❌ niet gelijk */
    else {
        if (password2.length > 0) {
            feedback.className = "alert alert-danger text-center font-weight-bold py-2 px-3";
            feedback.innerHTML =
                '<i class="fas fa-times mr-2"></i>Wachtwoorden komen niet overeen!<i class="fas fa-times ml-2"></i>';
            feedback.classList.remove("d-none");
        } else {
            feedback.classList.add("d-none");
            feedback.innerHTML = "";
        }
        submitBtn.disabled = true;
    }
}

// Decimal formaat checker
function isValid(el) {
	return el.match(/^\d+(?:[\.]\d{0,2})?$/);
	}  

function formatDate(datum) {
	var formatted = '';
	
	if (datum && datum.trim() !== "") {
		formatted = (new Date(datum).toLocaleDateString("nl-BE", {
		  day: "2-digit",
		  month: "2-digit",
		  year: "numeric"
		}));
	}
	
	return formatted;
	}

function formatEuro(value) {
	var formatted = '';

	if (value) {
		formatted = new Intl.NumberFormat('nl-BE', { 
		  style: 'currency', 
		  currency: 'EUR' 
		}).format(value);
		}

	return formatted;
	}

function formatIban(input) {
	// Remove everything except letters and digits
	let v = input.value.replace(/[^A-Z0-9]/gi, '').toUpperCase();

	// Ensure it starts with BE
	if (!v.startsWith('BE')) {
		v = 'BE' + v.replace(/^BE/i, '');
	}

	// Only allow BE + digits, max 16 characters (BE + 14 digits)
	v = v.replace(/[^A-Z0-9]/g, '');   // Strip anything that's not alphanumeric
	v = v.substring(0, 16);           // Max: BE + 14 digits

	// Enforce digits-only rule after BE
	if (v.length > 2) {
		v = v.substring(0, 2) + v.substring(2).replace(/\D/g, '');
	}

	// Format visually as BE12 3456 7890 1234
	let formatted = v.replace(/(.{4})/g, '$1 ').trim();

	input.value = formatted;
}

function togglePassword() {
    const input = document.getElementById("inputPasswordLogin");
    const icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈"; // zichtbaar → toon ander icoon
    } else {
        input.type = "password";
        icon.textContent = "👁️"; // verborgen → terug naar oog
    }
}

/*
function waitingWheel() {
	if (document.readyState !== "complete") {
		document.querySelector(	"body").style.visibility = "hidden";
		document.querySelector(	"body").classList.add('bodyHidden');
		document.querySelector(	"#loader").style.visibility = "visible";
	} else {
		document.querySelector(	"#loader").style.display = "none";
		document.querySelector(	"body").style.visibility = "visible";
		document.querySelector(	"body").classList.remove('bodyHidden');
	}
}
	
document.onreadystatechange = function () {
	if (document.readyState !== "complete") {
	} else {
		document.querySelector(	"#loader").style.display = "none";
		document.querySelector(	"body").style.visibility = "visible";
		document.querySelector(	"body").classList.remove('bodyHidden');
	}
};
*/

	function showSuccessMessage(text) {
		const container = document.getElementById("fixedMessageContainer");

		container.innerHTML = `
			<div class="alert alert-success alert-dismissible fade show floating-alert" role="alert">
				<strong>Success!</strong> ${text}
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
			</div>
		`;

		// Auto close after 3 seconds
		setTimeout(() => {
			$('.alert').alert('close');
		}, 3000);
	}

	function showErrorMessage(text) {
		const container = document.getElementById("fixedMessageContainer");

		container.innerHTML = `
			<div class="alert alert-danger alert-dismissible fade show floating-alert" role="alert">
				<strong>Fout!</strong> ${text}
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
			</div>
		`;

		setTimeout(() => {
			$('.alert').alert('close');
		}, 5000);
	}

  (function($){
    const DEFAULTS = {
      message: "Loading…",
      overlay: true,      // full-page overlay by default
      inline: false,      // if true, attaches spinner inside the given element
      box: true,          // show the white box around the spinner for overlay
      ariaLabel: "Loading content",
      spinnerClass: "",   // extra class for spinner
      overlayId: 'jq-loader-overlay' // id for overlay DOM element
    };

    /* Build overlay DOM lazily */
    function buildOverlay() {
      if ($('#' + DEFAULTS.overlayId).length) return $('#' + DEFAULTS.overlayId);
      const $overlay = $('<div>', {
        id: DEFAULTS.overlayId,
        class: 'jq-loader-overlay',
        role: 'status',
        'aria-live': 'polite',
        'aria-label': DEFAULTS.ariaLabel
      });

      const $box = $('<div>').addClass('jq-loader-box');
      const $spinner = $('<div>').addClass('jq-spinner');
      const $msg = $('<div>').addClass('jq-loader-msg').text(DEFAULTS.message);

      $box.append($spinner).append($msg);
      $overlay.append($box);
      $('body').append($overlay);
      return $overlay;
    }

    /* Show global overlay */
    $.showLoader = function(opts = {}) {
      const o = $.extend({}, DEFAULTS, opts);
      const $overlay = buildOverlay();
      // set message / aria
      $overlay.find('.jq-loader-msg').text(o.message);
      $overlay.attr('aria-label', o.ariaLabel || o.message);
      if (!o.box) $overlay.find('.jq-loader-box').addClass('jq-hidden'); else $overlay.find('.jq-loader-box').removeClass('jq-hidden');
      if (o.spinnerClass) $overlay.find('.jq-spinner').addClass(o.spinnerClass);
      // show
      $overlay.addClass('show');
      return $overlay;
    };

    /* Hide global overlay */
    $.hideLoader = function() {
      $('#' + DEFAULTS.overlayId).removeClass('show');
      return $('#' + DEFAULTS.overlayId);
    };

    /* Element-level plugin: show/hide inline loader inside each matched element */
    $.fn.showLoader = function(opts = {}) {
      const o = $.extend({}, DEFAULTS, opts, { inline: true });
      return this.each(function(){
        const $el = $(this);
        // if already has an inline loader, don't add again
        if ($el.data('jqInlineLoader')) return;
        const $container = $('<div>').addClass('jq-inline-loader').attr({
          role: 'status',
          'aria-live': 'polite',
          'aria-label': o.ariaLabel || o.message
        });
        const $spinner = $('<div>').addClass('jq-spinner small').addClass(o.spinnerClass || '');
        const $msg = $('<span>').text(o.message).addClass('jq-loader-msg');
        $container.append($spinner).append($msg);
        // append after the element's content (you can adjust to prepend)
        $el.append($container);
        $el.data('jqInlineLoader', $container);
      });
    };

    $.fn.hideLoader = function() {
      return this.each(function(){
        const $el = $(this);
        const $container = $el.data('jqInlineLoader');
        if ($container) {
          $container.remove();
          $el.removeData('jqInlineLoader');
        }
      });
    };

    // Optionally: auto hide overlay on escape key
    $(document).on('keydown', function(e){
      if (e.key === 'Escape') {
        $.hideLoader();
      }
    });

  })(jQuery);