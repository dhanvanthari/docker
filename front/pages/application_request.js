import AutocompletedAddressForm from '../services/address/AutocompletedAddressForm';
import AddressObject from '../services/address/AddressObject';

export default (formToDisplay) => {
    /**
     * Volunteer request form
     */
    const volunteerForm = dom('form[name="volunteer_request"]');
    const volunteerRootIdField = `#${volunteerForm.name}`;

    const volunteerCountryField = dom(`${volunteerRootIdField}_address_country`);
    const volunteerCityNameField = dom(`${volunteerRootIdField}_address_cityName`);
    const volunteerZipCodeField = dom(`${volunteerRootIdField}_address_postalCode`);

    const volunteerAddress = new AddressObject(
        dom(`${volunteerRootIdField}_address_address`),
        volunteerZipCodeField,
        volunteerCityNameField,
        null,
        volunteerCountryField
    );

    const volunteerAutocompleteAddressForm = new AutocompletedAddressForm(
        dom('form[name="volunteer_request"] .address-autocomplete'),
        dom('form[name="volunteer_request"] .address-block'),
        volunteerAddress,
        dom('form[name="volunteer_request"] .address-autocomplete-help-message')
    );

    volunteerAutocompleteAddressForm.once('changed', () => {
        volunteerCountryField.dispatchEvent(new CustomEvent('change', {
            target: volunteerCountryField,
            detail: {
                zipCode: volunteerZipCodeField.value,
                cityName: volunteerCityNameField.value,
            },
        }));
    });

    volunteerAutocompleteAddressForm.buildWidget();

    volunteerZipCodeField.dispatchEvent(new Event('input'));


    /**
     * Running mate request form
     */
    const runningMateForm = dom('form[name="running_mate_request"]');
    const runningMateRootIdField = `#${runningMateForm.name}`;

    const runningMateCountryField = dom(`${runningMateRootIdField}_address_country`);
    const runningMateCityNameField = dom(`${runningMateRootIdField}_address_cityName`);
    const runningMateZipCodeField = dom(`${runningMateRootIdField}_address_postalCode`);

    const runningMateAddress = new AddressObject(
        dom(`${runningMateRootIdField}_address_address`),
        runningMateZipCodeField,
        runningMateCityNameField,
        null,
        runningMateCountryField
    );

    const runningMateAutocompleteAddressForm = new AutocompletedAddressForm(
        dom('form[name="running_mate_request"] .address-autocomplete'),
        dom('form[name="running_mate_request"] .address-block'),
        runningMateAddress,
        dom('form[name="running_mate_request"] .address-autocomplete-help-message')
    );

    runningMateAutocompleteAddressForm.once('changed', () => {
        runningMateCountryField.dispatchEvent(new CustomEvent('change', {
            target: runningMateCountryField,
            detail: {
                zipCode: runningMateZipCodeField.value,
                cityName: runningMateCityNameField.value,
            },
        }));
    });

    runningMateAutocompleteAddressForm.buildWidget();

    runningMateZipCodeField.dispatchEvent(new Event('input'));

    $('.em-form__file--area').bind('change', (event) => {
        const $input = $(event.currentTarget);
        const selectedFileName = $input.val();
        const $fileName = $input.siblings('.em-form__file--name');
        const $label = $input.siblings('.em-form__file--label');

        if (0 < $input.length && 0 < selectedFileName.length) {
            $fileName.html(selectedFileName.split('\\').pop());
            $label.html('Modifier la pièce jointe');
        }
    });

    // Form swapper
    if ('RunningMateRequest' === formToDisplay) {
        $('#volunteer-form').hide();
        $('#js-RunningMate').addClass('selected');
    } else {
        $('#running-mate-form').hide();
        $('#js-Volunteer').addClass('selected');
    }

    $('#js-rolePicker .pick-btn').click((event) => {
        const $this = $(event.currentTarget);
        $this.addClass('selected');
        $this.siblings().removeClass('selected');

        if ('js-RunningMate' === $this.attr('id')) {
            $('#volunteer-form').fadeOut(400, () => {
                $('#running-mate-form').fadeIn();
            });
        }

        if ('js-Volunteer' === $this.attr('id')) {
            $('#running-mate-form').fadeOut(400, () => {
                $('#volunteer-form').fadeIn();
            });
        }
    }).hover(
        event => $(event.currentTarget).siblings().addClass('fade'),
        event => $(event.currentTarget).siblings().removeClass('fade')
    );
};
