function createEdit() {
    $("#formSubmit").on("keyup", function (e) {
        $('[name="win_prize"]').val(parseLocaleNumber($("#win_prize").val()));
    });

    $("#win_prize").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });
}
