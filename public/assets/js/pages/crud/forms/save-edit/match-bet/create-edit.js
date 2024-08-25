function tipsterSeasonChange(route) {
    $('[name="tipster_season_id"]').on("change", function (e) {
        let start_date_match = $('[name="start_date_match"]').val();
        let end_date_match = $('[name="end_date_match"]').val();
        $.ajax({
            url: route,
            type: "POST",
            data: {
                id: this.value,
                start_date_match: start_date_match,
                end_date_match: end_date_match,
            },
            dataType: "json", // added data type
            success: function (res) {
                $('[name="football_match_id"]')
                    .find("option")
                    .remove()
                    .end()
                    .append(
                        $("<option>", {
                            value: "",
                            text: "== Select Football Match ==",
                        })
                    );
                res.data.forEach(function (data) {
                    $('[name="football_match_id"]').append(
                        $("<option>", {
                            value: data.id,
                            text:
                                data.home_team_name +
                                " - " +
                                data.away_team_name,
                        })
                    );
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('[name="football_match_id"]')
                    .find("option")
                    .remove()
                    .end()
                    .append(
                        $("<option>", {
                            value: "",
                            text: "== Select Football Match ==",
                        })
                    );
                resetInput();
            },
        });
    });

    $('[name="start_date_match"]').on("change", function (e) {
        $('[name="tipster_season_id"]').trigger("change");
    });

    $('[name="end_date_match"]').on("change", function (e) {
        $('[name="tipster_season_id"]').trigger("change");
    });
}

function footballMatchChange(route) {
    $('[name="football_match_id"]').on("change", function (e) {
        $.ajax({
            url: route,
            type: "POST",
            data: { id: this.value },
            dataType: "json", // added data type
            success: function (res) {
                $("#away_team input").prop("disabled", false);
                $("#home_team input").prop("disabled", false);
                $('[name="home_team_football_team_id"]').val(
                    res.data[0]["home_team_id"]
                );
                $('[name="away_team_football_team_id"]').val(
                    res.data[0]["away_team_id"]
                );
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                resetInput();
            },
        });
    });
}

function createEdit() {
    $(".reset").on("click", function (e) {
        resetInput();
    });

    $("#formSubmit").on("keyup", function (e) {
        $('[name="odds_over"]').val(parseLocaleNumber($("#odds_over").val()));
        $('[name="home_team_odds"]').val(
            parseLocaleNumber($("#home_team_odds").val())
        );
        $('[name="away_team_odds"]').val(
            parseLocaleNumber($("#away_team_odds").val())
        );
        $('[name="odds_under"]').val(parseLocaleNumber($("#odds_under").val()));
        $('[name="handicap"]').val(parseLocaleNumber($("#handicap").val()));
        $('[name="bet_price"]').val(parseLocaleNumber($("#bet_price").val()));
        $('[name="big_bet_price"]').val(
            parseLocaleNumber($("#big_bet_price").val())
        );
        $('[name="home_team_handicap"]').val(
            parseLocaleNumber($("#home_team_handicap").val())
        );
        $('[name="away_team_handicap"]').val(
            parseLocaleNumber($("#away_team_handicap").val())
        );
        $('[name="over_under_handicap"]').val(
            parseLocaleNumber($("#over_under_handicap").val())
        );
    });

    $("#home_team_odds").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#away_team_odds").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#away_team_handicap").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#home_team_handicap").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#odds_under").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#odds_over").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#handicap").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });

    $("#bet_price").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });
    $("#big_bet_price").inputmask({
        alias: "decimal",
        rightAlign: false,
        groupSeparator: ".",
        autoGroup: true,
        reverse: true,
    });
}
