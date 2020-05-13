$(document).ready(function () {
    $('#liste-demande__').DataTable({
        'paging': false,
        'lengthChange': true,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false
    });
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        daysOfWeekDisabled: [0, 6],
        weekStart: 1
    });

    //input checkbox & radio style
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
//ajax setup
    jQuery.ajaxSetup({
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            $('#loading').hide();
        },
        success: function () {
            //$('#loading').hide();
        },
        error: function () {
            $('#loading').hide();
        }
    });
    $('.colorpicker').colorpicker();
    //Initialize Select2 Elements
    $('.select2').select2();
    addInterim();
    addPieceJointe();
    addEtapeWf();

});
$('.date-range-start').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    daysOfWeekDisabled: [0, 6],
    weekStart: 1
}).on('changeDate', function (selected) {
    startDate = new Date(selected.date.valueOf());
    startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
    $('.date-range-end').datepicker('setStartDate', startDate);
});
$('.date-range-end').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    daysOfWeekDisabled: [0, 6],
    weekStart: 1
}).on('changeDate', function (selected) {
    FromEndDate = new Date(selected.date.valueOf());
    FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
    $('.date-range-start').datepicker('setEndDate', FromEndDate);
});

/* initialize the calendar
 -----------------------------------------------------------------*/
$('#calendar').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    buttonText: {
        today: 'Ajourd\'hui',
        month: 'Mois',
        week: 'Semaine',
        day: 'Jour'
    },
    editable: false,
    droppable: false,
    lang: 'fr',
    events: $('#url-event-calendar').val(),
    eventMouseover: function (data, event, view) {

        tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:' + data.backgroundColor + ';position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;border-radius: 10px;">' +
                '<table border=0  style="color:#FFF;	font-family:sans-serif ;font-size:11px;text-shadow: 0px 1px 0px #000;box-shadow: 0px 0px 0px rgba(255, 255, 255, 0.5) inset;transition: #000 5000ms ease-in 0s;	border-radius: 3px;vertical-align: top;">' +
                '<tr><td colspan=2 style="align:center;background-color:#000">' + data.title + '</td></tr>' +
                '<tr><td width="30%">Date début :</td><td align="left">' + data.dateStart + '</td></tr>' +
                '<tr><td width="30%">Date fin :</td><td align="left">' + data.dateEnd + '</td></tr>' +
                '<tr><td width="30%">Date retour :</td><td align="left">' + data.dateRetour + '</td></tr>' +
                '<tr><td width="30%">Nbr de jour :</td><td align="left">' + data.totalDays + '</td></tr>' +
                '<tr><td width="30%">statut :</td><td align="left">' + data.status + '</td></tr>' +
                '</table>' +
                '</div>';
        $("body").append(tooltip);
        $(this).mouseover(function (e) {
            $(this).css('z-index', 10000);
            $('.tooltiptopicevent').fadeIn('500');
            $('.tooltiptopicevent').fadeTo('10', 1.9);
        }).mousemove(function (e) {
            $('.tooltiptopicevent').css('top', e.pageY + 10);
            $('.tooltiptopicevent').css('left', e.pageX + 20);
        });
    },
    eventMouseout: function (data, event, view) {
        $(this).css('z-index', 8);
        $('.tooltiptopicevent').remove();
    },
    dayClick: function () {
        tooltip.hide();
    },
    eventResizeStart: function () {
        tooltip.hide();
    },
    eventDragStart: function () {
        tooltip.hide();
    },
    viewDisplay: function () {
        tooltip.hide();
    },
    eventAfterRender: function (event, element, view) {
        if (event.startdj == "1") {
            var containerWidth = 150;
            //var containerWidth = jQuery(element).offsetParent().siblings("table").find(".fc-day-content").width();

            // half a day
            var elementWidth = parseInt(containerWidth / 2);
            // set width of element
            jQuery(element).css('width', elementWidth + "px");
        }

        if (event.enddj == "1") {
            var containerWidth = 150;
            //var containerWidth = jQuery(element).offsetParent().siblings("table").find(".fc-day-content").width();

            // half a day
            var elementWidth = parseInt(containerWidth / 2);
            // set width of element
            jQuery(element).css('width', elementWidth + "px");
            jQuery(element).css('margin-left', elementWidth + "px");
        }

    }
});


//add etape workflow
function addEtapeWf()
{
    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéres    se.
    var $container = $('div#modeleWorkflowSteps');
    // On ajoute un lien pour ajouter une nouvelle etape
    //var $lienAjout = $('<a href="#" id="ajout_etape"><span class="btn"><button type="button" name="retour" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Ajouter étape</button></span></a>');
    var $lienAjout = $('#ajout_etape');
    //$container.append($lienAjout);
    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $lienAjout.click(function (e) {
        addRow($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });
    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquem    ent
    var index = $container.find(':input').length;
    // On ajoute un premier champ directement s'il n'en existe pas déjà un (cas d'un nouvel article par exemple).
    if (index == 0) {
        //addRow($container);
    } else {
        // Pour chaque ligne créée, on ajoute un lien de suppression
        $container.children('div').each(function () {
            ajouterLienSuppression($(this));
        });
    }
    // La fonction qui ajoute un formulaire Etape
    function addRow($container) {
        // Dans le contenu de l'attribut « data-prototype »,on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du ch    amp
        var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'Etape n' + (index + 1)).replace(/__name__/g, index));
        //var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, ''));
        // On ajoute au prototype un lien pour pouvoir supprimer la etape
        ajouterLienSuppression($prototype);
        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.find('table.table-step').append($prototype);
        //set default value for order step
        $order = $container.find($('#modele_workflow_modeleWorkflowSteps_' + index + '_stepOrder'));
        $order.val(index);
        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une etape
    function ajouterLienSuppression($prototype) {
        // Création du lien
        $lienSuppression = $('<a href="#"><span class="btn"><button type="button" name="remove" class="btn btn-danger"><i class="fa fa-remove"></i>&nbsp;&nbsp;Supprimer</button></span></a>');
        // Ajout du lien
        $prototype.append($lienSuppression);
        // Ajout du listener sur le clic du lien
        $lienSuppression.click(function (e) {
            $prototype.remove();
            e.preventDefault();
            index--;

            return false;
        });
    }
}

/*$(document).on('change', '#demande_conge_dateEndMorning, #demande_conge_dateStartAfternoon', function () {
 var $checked = $(this).prop('checked');
 var $totalDays = parseFloat($('#demande_conge_totalDays').val());
 if ($checked) {
 parseFloat($('#demande_conge_totalDays').val($totalDays - 0.5));
 } else {
 parseFloat($('#demande_conge_totalDays').val($totalDays + 0.5));
 }
 //setDateRetour();
 });*/
$(document).on('change', '#demande_conge_dateStartMorning', function () {
    isFilledDate();
    $('label[for=demande_conge_dateStartAfternoon]').removeClass('active');
    $('label[for=demande_conge_dateStartAfternoon]').removeClass('focus');
    $('#demande_conge_dateStartAfternoon').prop('checked', false);
    $('#demande_conge_dateStartAfternoon').trigger('change');
    $(this).prop('checked', true);
    $('label[for=demande_conge_dateStartMorning]').toggleClass('active');
    $(this).addClass('active');
    $(this).toggleClass('focus');
});


$(document).on('change', '#demande_conge_dateStartAfternoon', function () {
    isFilledDate();
    $('label[for=demande_conge_dateStartMorning]').removeClass('active');
    $('#demande_conge_dateStartMorning').prop('checked', false);
    $(this).toggleClass('active');
    $(this).toggleClass('focus');
    setNbrJour();
    checkChevauchementDate();
});

$(document).on('change', '#demande_conge_dateEndMorning', function () {
    isFilledDate();
    $('#demande_conge_dateEndAfternoon').prop('checked', false);
    $('label[for=demande_conge_dateEndAfternoon]').removeClass('active');
    $(this).toggleClass('active');
    //$(this).toggleClass('focus');
    setNbrJour();
    setDateRetour();
    checkChevauchementDate();
});

$(document).on('change', '#demande_conge_dateEndAfternoon', function () {
    isFilledDate();
    $('#demande_conge_dateEndMorning').prop('checked', false);
    $('#demande_conge_dateEndMorning').trigger('change');
    $('label[for=demande_conge_dateEndMorning]').removeClass('active');
    $('label[for=demande_conge_dateEndAfternoon]').toggleClass('active');
    $(this).prop('checked', true);
    $(this).toggleClass('active');
    //$(this).toggleClass('focus');

});

$(document).on('change', '#demande_conge_dateStart', function () {
    if ($('#demande_conge_dateEnd').val() !== '') {
        setNbrJour();
        checkChevauchementDate();
        if ($('#demande_conge_dateRetour').val() == '') {
            setDateRetour();
        }
    }
});
$(document).on('change', '#demande_conge_demandor', function () {
    checkChevauchementDate();
});
$(document).on('change', '#demande_conge_dateEnd', function () {
    isFilledDate();
    checkChevauchementDate();
    setDateRetour();
    setNbrJour();
});
function setDateRetour()
{
    var $url = $('#url-date-retour').val();
    var $dateEnd = $("#demande_conge_dateEnd").val();
    var $isHalf = $("#demande_conge_dateEndMorning").prop('checked') ? 1 : 0;
    if ($dateEnd !== '') {
        $.ajax({
            url: $url,
            data: {
                date_retour: $dateEnd,
                is_half: $isHalf
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#demande_conge_dateRetour').val(data);
            },
            error: function () {
                alert('error');
            }
        });
    }
}
function setNbrJour()
{
    var $dateStart = $("#demande_conge_dateStart").val();
    var $dateEnd = $("#demande_conge_dateEnd").val();
    var $isHalfStart = $("#demande_conge_dateStartAfternoon").prop('checked') ? 1 : 0;
    var $isHalfEnd = $('#demande_conge_dateEndMorning').prop('checked') ? 1 : 0;
    var $url = $('#url-total-days').val();
    if ($dateStart !== '' && $dateEnd !== '') {
        $.ajax({
            url: $url,
            data: {
                date_start: $dateStart,
                date_end: $dateEnd,
                is_half_start: $isHalfStart,
                is_half_end: $isHalfEnd
            },
            type: 'POST',
            dataType: 'json',
            //async: true,
            success: function (data) {
                $('#demande_conge_totalDays').val(data);
            },
            error: function () {
                alert('error');
            }
        });
    }
}
//vérifier si la date de début et la date de fin sont toutes renseigner
function isFilledDate()
{
    var $debut = $('#demande_conge_dateStart').val();
    var $fin = $('#demande_conge_dateEnd').val();
    if ($debut === '' || $fin === '') {
//alert('Veuillez renseigner la date de début et la date de fin');
        $('#demande_conge_dateStartMorning').prop('checked', false);
        $('#demande_conge_dateStartAfternoon').prop('checked', false);
        $('#demande_conge_dateEndMorning').prop('checked', false);
        $('#demande_conge_dateEndAfternoon').prop('checked', false);

        $('label[for=demande_conge_dateStartMorning]').removeClass('active');
        $('label[for=demande_conge_dateStartAfternoon]').removeClass('active');
        $('label[for=demande_conge_dateEndMorning]').removeClass('active');
        $('label[for=demande_conge_dateEndAfternoon]').removeClass('active');
        $('#demande_conge_totalDays').val('');

        return;
    }

    return true;
}

function checkChevauchementDate()
{
    var $dateStart = $("#demande_conge_dateStart").val();
    var $dateEnd = $("#demande_conge_dateEnd").val();
    var $demandorId = $('#demande_conge_demandor').val();
    var $isHalfStart = $("#demande_conge_dateStartAfternoon").prop('checked') ? 1 : 0;
    var $isHalfEnd = $('#demande_conge_dateEndMorning').prop('checked') ? 1 : 0;
    var $url = $('#url-check-date').val();
    if ($dateStart !== '' && $dateEnd !== '') {
        $.ajax({
            url: $url,
            data: {
                date_start: $dateStart,
                date_end: $dateEnd,
                demandor_id: $demandorId,
                is_half_start: $isHalfStart,
                is_half_end: $isHalfEnd
            },
            type: 'POST',
            dataType: 'json',
            //async: true,
            success: function (data) {
                if (data.iTotal > 0) {
                    $('#btn-save-dc').attr('disabled', 'disabled');
                    alert('Une demande existe déjà avec les dates selectionnées');
                } else {
                    $('#btn-save-dc').removeAttr('disabled');
                }
            },
            error: function () {
                alert('error');
            }
        });
    }
}

//disable btn submit after click
$(document).on('click', '.btn', function () {
    //$('.btn').prop('disabled', true);
});

//disable btn reset after click
$(document).on('click', '.btn-reset', function () {
    $(this).prop('disabled', true);
    $('input[type=text], select').val('');
    $('form').submit();
});
// remove row validator step
$(document).on('click', '.remove-validator-step', function (e) {
    e.preventDefault();
    var $url = $('.url-remove-validator-step').val();
    var $id = $(this).data('id');
    $.ajax({
        url: $url,
        data: {
            id: $id
        },
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            $('tr.row-' + $id).remove();
        }
    });
});
$(document).on('change', '.validator-selected', function () {
    var $name = $('.validator-selected option[value=' + $(this).val() + ']:first').text();
    if ($(this).val() == 0) {
        $('.input-validator-id').val(0);
        $('.input-validator-id').attr('data-name', '');
    } else {
        $('.input-validator-id').val($(this).val());
        $('.input-validator-id').attr('data-name', $name);
    }
});
//click btn add validator
$(document).on('click', '.btn-add-validator', function (e) {
    e.preventDefault();
    var $url = $(this).data('url');
    var $stepId = $(this).data('step');
    var $validatorId = $('.input-validator-id').val();
    if ($validatorId > 0) {
        $.ajax({
            url: $url,
            data: {
                step_id: $stepId,
                validator_id: $validatorId
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.exist === true) {
                    alert('Ce validateur existe déjà');
                } else {
                    $('.table-step-validator-' + $stepId).append("<tr class=row-" + response.id + "><td>" + response.name + "</td><td><a href='#' data-id=" + response.id + " class='remove-validator-step'>Supprimer</a></td></tr>");
                }
            }
        });
    } else {
        alert('Veuillez selectionner un validateur');
    }
});
//clik btn validate/refuse demande
$(document).on('click', '.btn-validate, .btn-refus', function () {
    var $status = $(this).data('status');
    $('.btn-validate, .btn-refus, .btn-retour').prop('disabled', true);
    $('#demande_history_validation_validationStatus').val($status);
    $('form[name="demande_history_validation"]').submit();
});

//planning conge filtered userId
$(document).on('change', '#filterUsersId', function () {
    location.href = $('#filterUsersId option:selected').data('url');
});

//add interim de fonction et validation
function addInterim()
{
    var $container = $('div#interim');
    // On ajoute un lien pour ajouter une nouvelle etape
    //var $lienAjout = $('<a href="#" id="ajout_interim"><span class="btn"><button type="button" name="retour" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Ajouter interim</button></span></a>');
    var $lienAjout = $('#ajout_interim');
    $lienAjout.click(function (e) {
        addRowInterim($container);
        e.preventDefault();
        return false;
    });
    var index = $container.find(':input').length;
    if (index == 0) {
        //addRowInterim($container);
    } else {
        $container.children('div').each(function () {
            ajouterLienSuppressionInterim($(this));
        });
    }
    function addRowInterim($container) {
        var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'Interim n' + (index + 1)).replace(/__name__/g, index));
        ajouterLienSuppressionInterim($prototype);
        $container.find('table.table-interim').append($prototype);
        index++;
    }

    function ajouterLienSuppressionInterim($prototype) {
        $lienSuppression = $('<a href="#"><span class="btn"><button type="button" name="remove" class="btn btn-danger"><i class="fa fa-remove"></i>&nbsp;&nbsp;Supprimer</button></span></a>');
        $prototype.append($lienSuppression);
        $lienSuppression.click(function (e) {
            $prototype.remove();
            e.preventDefault();
            index--;

            return false;
        });
    }
}
var $iFiles = 0;
//add pièce justificative demande
function addPieceJointe()
{
    var $container = $('div#document');
    var $lienAjout = $('#ajout_document');
    $lienAjout.click(function (e) {
        addNewDocument($container);
        /* appliquer dynamiquement le style filestyle */
        $(".filestyle").each(function () {
            var $this = $(this), options = {
                input: $this.attr("data-input") === "false" ? false : true,
                icon: $this.attr("data-icon") === "false" ? false : true,
                buttonBefore: $this.attr("data-buttonBefore") === "true" ? true : false,
                disabled: $this.attr("data-disabled") === "true" ? true : false,
                size: $this.attr("data-size"),
                buttonText: $this.attr("data-buttonText"),
                buttonName: $this.attr("data-buttonName"),
                iconName: $this.attr("data-iconName"),
                badge: $this.attr("data-badge") === "false" ? false : true,
                placeholder: $this.attr("data-placeholder")
            };
            $this.filestyle(options);
        });
        e.preventDefault();

        return false;
    });
    var index = $container.find(':input').length;
    if (index == 0) {
        //addNewDocument($container);
    } else {
        $container.children('div').each(function () {
            ajouterLienSuppressionDocument($(this));
        });
    }
    function addNewDocument($container) {
        var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'Pièce jointe' + (index + 1)).replace(/__name__/g, index));

        $container.append($prototype);
        ajouterLienSuppressionDocument($prototype);
        index++;
        $iFiles++;
    }

    function ajouterLienSuppressionDocument($prototype) {
        $lienSuppression = $('<a href="#"><span class="btn"><button type="button" name="remove" class="btn btn-danger"><i class="fa fa-remove"></i>&nbsp;&nbsp;Supprimer</button></span></a>');
        $prototype.append($lienSuppression);
        $lienSuppression.click(function (e) {
            $prototype.remove();
            e.preventDefault();
            index--;
            $iFiles--;

            return false;
        });
    }
}

/* click link remove step */
$(document).on('click', '.remove-step', function (e) {
    e.preventDefault();
    var $id = $(this).data('id');
    var $url = $(this).data('url');
    $.ajax({
        url: $url,
        data: {
            id: $id
        },
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            $('.table-step tr.step-' + $id).remove();
            location.href = location.href;
        }
    });
});

//click btn save demande conge
$(document).on('click', '#btn-save-dc', function () {
    var $iCongePayeId = 1;//conge paye id [à dynamiser]
    var $iSelectedTypeConge = $('#demande_conge_typeConge').val();
    $zFile = $('#document input[type=file]').val();
    if (($iFiles <= 0 || $zFile == 'undefined' || $zFile == '') && $iSelectedTypeConge != $iCongePayeId) {
        alert('Veuillez télécharger les pièces justificatives');

        return false;
    }
    $('#form-dc-create').submit();
});

//prepare filter search export
function prepareFilterExport()
{
    $('#numero-dc').val($('#demande_conge_search_numero').val());
    $('#matricule-dc').val($('#demande_conge_search_matricule').val());
    $('#initiator-dc').val($('#demande_conge_search_initiator').val());
    $('#demandor-dc').val($('#demande_conge_search_demandor').val());
    $('#type-dc').val($('#demande_conge_search_typeConge').val());
    $('#date-debut-dc').val($('#demande_conge_search_dateStart').val());
    $('#date-fin-dc').val($('#demande_conge_search_dateEnd').val());
    $('#direction-dc').val($('#demande_conge_search_direction').val());
    $('#etat-dc').val($('#demande_conge_search_demandeStatus').val());
}

//click btn export dc
$(document).on('click', '#btn-export-dc', function () {
    prepareFilterExport();
});

//click btn accept masse validation
$(document).on('click', '#btn-dialog-masse-validation', function () {
    $('#form-masse-validation').attr('action', $('#ulr-masse-validation').val());
    $('#form-masse-validation').attr('method', 'POST');
    //$('form').submit();
    $('input[type=submit]').trigger('click');
});

//transfert de validation
$(document).on('click', '.btn-add-validator-transfert', function () {
    var $url = $('#url-transfert').val();
    var $demandeId = $('#demandeId').val();
    var $interim = $('#transfert-to').val();
    if ($interim > 0 && $demandeId !== '') {
        $.ajax({
            url: $url,
            data: {
                demandeId: $demandeId,
                interimId: $interim
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.msg === 'ok') {
                    //$('#transfert-to').val('');
                    location.href = location.href;
                } else {
                    //duplicate entry
                    $('.already-validator').css('display', 'block');
                }
            },
            error: function () {
                alert('error');
            }
        });
    } else {
        $('.empty-validator').css('display', 'block');
    }
});