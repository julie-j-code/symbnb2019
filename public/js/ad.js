$('#add-image').click(function(){
    //je récupère le numéro des champs que je vais créer
    const index = +$('#widgets-counter').val();
    //const index = $('#ad_images div.form-group').length;
    //je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    //j'injecte ce code dans ma div
    $('#ad_images').append(tmpl);
    
    $('#widgets-counter').val(index + 1);

    //je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

//pour mettre à jour mon compteur et corriger le bug qui faisait qu'il faisait doublon avec le premier zero
function updateCounter(){
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButtons();