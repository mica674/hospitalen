<?php

// Appel des constantes
require_once(__DIR__ . '/../config/constants.php');

// Appel du model
require_once(__DIR__ . '/../models/Patient.php');
$patients = Patient::getPatientsList();
// Message si un patient vient d'être ajouté
if ($_GET['patientEdited']??'' == 1) {
    echo 'Le patient a été modifié avec succès félicitations, excellente journée à vous !';
}



// Appel du header
include(__DIR__ . '/../views/templates/header.php');

// Appel de la view
include(__DIR__ . '/../views/clients/patientsList.php');

// Appel du footer
include(__DIR__ . '/../views/templates/footer.php');