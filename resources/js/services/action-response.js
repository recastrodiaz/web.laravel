Dms.action.responseHandler = function (httpStatusCode, actionUrl, response) {
    if (typeof response.redirect !== 'undefined') {
        if (typeof response.message !== 'undefined') {
            Cookies.set('dms-flash-alert', {
                message: response.message,
                type: response.message_type || 'success'
            });
        }

        Dms.link.goToUrl(response.redirect);
        return;
    }

    if (typeof response.message !== 'undefined') {
        Dms.alerts.add(response.message_type || 'success', response.message);
    }

    if (typeof response.files !== 'undefined') {
        var fileNames = [];


        $.each(response.files, function (index, file) {
            fileNames.push(file.name);
        });


        swal({
            html: true,
            title: "Downloading files",
            text: "Please wait while your download begins.\r\n Files: " + fileNames.join(', '),
            type: "info",
            showConfirmButton: false,
            showLoaderOnConfirm: true
        });

        $.each(response.files, function (index, file) {
            $('<iframe />')
                .attr('src', Dms.config.routes.downloadFile(file.token))
                .css('display', 'none')
                .appendTo($(document.body));
        });

        var downloadsBegun = 0;
        var checkIfDownloadsHaveBegun = function () {

            $.each(response.files, function (index, file) {
                var fileCookieName = 'file-download-' + file.token;

                if (Cookies.get(fileCookieName)) {
                    downloadsBegun++;
                    Cookies.remove(fileCookieName)
                }
            });

            if (downloadsBegun < response.files.length) {
                setTimeout(checkIfDownloadsHaveBegun, 100);
            } else {
                swal.close();
            }
        };

        checkIfDownloadsHaveBegun();
    }

    if (typeof response.content !== 'undefined') {
        var contentDialog = $('.dms-content-dialog').first();

        contentDialog.find('.modal-title').text(response.content_title || '');

        var dialogBody = contentDialog.find('.modal-body');
        dialogBody.empty();

        if (response.iframe) {
            var iframe = $('<iframe />');
            iframe.addClass('dms-content-iframe');
            dialogBody.append(iframe);
            setTimeout(function () {
                var document = iframe.contents().get(0);
                document.open();
                document.write(response.content);
                document.close();
            }, 1);
        } else {
            dialogBody.html(response.content);
        }

        contentDialog.appendTo('body').modal('show');
        Dms.all.initialize(dialogBody);

        dialogBody.on('click', 'a[href]', function () {
            contentDialog.modal('hide');
        });
    }
};