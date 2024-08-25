function createEdit() {
    $("#formSubmit").on("keyup", function (e) {
        $('[name="initialize_balance"]').val(
            parseLocaleNumber($("#initialize_balance").val())
        );
    });

    $("#initialize_balance").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });
}
