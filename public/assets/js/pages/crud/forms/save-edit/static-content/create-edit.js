async function createEdit() {
    await ClassicEditor.create(document.querySelector("#content"), {
        fontColor: {
            colors: [
                {
                    color: "hsl(0, 0%, 0%)",
                    label: "Black",
                },
            ],
        },
        fontBackgroundColor: {
            colors: [
                {
                    color: "hsl(0, 75%, 60%)",
                    label: "Red",
                },
            ],
        },
    })
        .then((content) => {
            content.editing.view.document.on(
                "keydown",
                function (evt, data) {
                    if (data.keyCode == 32) {
                        const insertPosition =
                            content.model.document.selection.getFirstPosition();
                        content.model.change((writer) => {
                            writer.insertText("\xA0", insertPosition);
                        });

                        data.preventDefault();
                        evt.stop();
                    }
                },
                { priority: "highest" }
            );
        })
        .catch((error) => {
            console.error(error);
        });
}
