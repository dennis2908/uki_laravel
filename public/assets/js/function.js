function parseLocaleNumber(number) {
    if (number) {
        let locale = "en-US";
        var thousandSeparator = Intl.NumberFormat(locale)
            .format(11111)
            .replace(/\p{Number}/gu, "");
        var decimalSeparator = Intl.NumberFormat(locale)
            .format(1.1)
            .replace(/\p{Number}/gu, "");

        return parseFloat(
            number
                .replace(new RegExp("\\" + thousandSeparator, "g"), "")
                .replace(new RegExp("\\" + decimalSeparator), ".")
        );
    }
}

function resetInput() {
    $("#away_team input").prop("disabled", true);
    $("#away_team :input").val("");
    $('[name="home_team_handicap"]').val("");
    $("#home_team input").prop("disabled", true);
    $("#home_team :input").val("");
}
