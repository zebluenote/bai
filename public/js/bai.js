// On reçoit le status du carousel après mise à jour
// status = bool

/**
 * ADMIN - Gestion de l'affichage des boutons "toggler"
 * @param {*} label 
 * @param {*} element 
 * @param {*} status 
 * @param {*} isError 
 */
function toggleStatus(label, element, status, isError) {
    if (status) {
        element.attr('data-status', '1');
        element.prop('checked', true);
        element.attr('checked', 'checked');
        if (isError) {
            label.html('on <span class="fas fa-check" style="color:red;"></span>')
        } else {
            label.html('on <span class="fas fa-check" style="color:green;"></span>')
        }
    } else {
        element.attr('data-status', '');
        element.prop('checked', false);
        element.attr('checked', null);
        if (isError) {
            label.html('off <span class="fas fa-check" style="color:red;"></span>')
        } else {
            label.html('off <span class="fas fa-check" style="color:green;"></span>')
        }
    }
}

