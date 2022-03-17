define(["jquery", "core/config", "core/modal_factory", "core/modal_events",  "core/fragment", "core/ajax"],
    function($, Config, ModalFactory, ModalEvents, Fragment, Ajax) {
        function getBody(formdata) {
            const contextid = M.cfg.contextid;
            // Get the content of the modal.
            var params = {formdata};
            return Fragment.loadFragment("tool_translationmanager", "translation_form", contextid, params);
        }
        return {
            init: function () {
                $('body').on('click', '[data-action=\'translation-edit\']', function (e) {
                    e.preventDefault();
                    const recordid = $(this).attr('data-recordid');
                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        body: getBody(recordid)
                    }).then(function(modal) {
                        modal.getModal().addClass('modal-xl');
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                        });
                        modal.show();
                        modal.getRoot().on('submit', 'form', function(e) {
                            e.preventDefault();
                            var data = $('.mform').serialize();
                            var promise = Ajax.call([{
                                methodname: 'tool_translationmanager_update_data',
                                args: {jsonformdata: data}
                            }]);

                            promise[0].then( function() {
                                    modal.destroy();
                                    window.location.reload(true);
                            }
                            );
                        });
                    });
                });
            }
        };
    }
);



