<?php
session_start();

require_once(__DIR__ . '/../helpers/flash.php');

// Appel des constantes
require_once(__DIR__ . '/../config/constants.php');

// Appel du model
require_once(__DIR__ . '/../models/Patient.php');

try {
    
    // *VERIFICATIONS DES DONNEES DU FORMULAIRE 
    // *PUIS REDIRECTION SI DONNEES VALIDEES
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Si les données sont bien envoyées en POST

        // ?LASTNAME
    // Nettoyage de tout les caractères ASCII 1 à 32
    $lastname = trim(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS));
    
    // Validation des données
    if (empty($lastname)) { //Si $lastname est vide
        $error['lastname'] = 'Vous n\'avez pas renseigné votre "Nom"'; // Message d'erreur lastname vide
    } elseif (!filter_var($lastname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_LASTNAME . '/')))) { //Sinon si $lastname ne correspond pas à un format lastname
        $error['lastname'] = 'Le nom ne correspond pas au format requis pour un nom'; //Message d'erreur lastname format
    }
    if (empty($error['lastname'])){
        $lastname = ucfirst(strtolower($lastname));
    }

    // ?FIRSTNAME
    // Nettoyage de tout les caractères ASCII 1 à 32
    $firstname = trim(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS));

    // Validation des données
    if (empty($firstname)) { //Si $firstname est vide
        $error['firstname'] = 'Vous n\'avez pas renseigné votre "Prénom"'; // Message d'erreur firstname vide
    } elseif (!filter_var($firstname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_FIRSTNAME . '/')))) { //Sinon si $firstname ne correspond pas à un format firstname
        $error['firstname'] = 'Le prénom ne correspond pas au format requis pour un prénom'; //Message d'erreur firstname format
    }
    if (empty($error['firstname'])){
        $firstname = ucfirst(strtolower($firstname));
    }

    // ?EMAIL
    // Double nettoyage de l'email
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    
    // Validation des données
    if (empty($email)) { //Si $email est vide
        $error['email'] = 'L\'email n\'est pas renseigné'; //Message d'erreur EMAIL
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Sinon si $email ne correspond pas à un format d'adresse email
        $error['email'] = 'L\'email ne correspond pas au format requis pour un email'; //Message d'erreur EMAIL format
    }
    if (empty($error['email'])){
        $email = strtolower($email);
    }

    // ?PHONE NUMBER
    // Nettoyage des caractères autres que les chiffres & '+' & '-'
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    
    // Validation des données
    if (!empty($phone)) {
        if (!filter_var($phone, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_PHONE_NUMBER . '/')))) { //Sinon si $phone ne correspond pas à un format numéro de téléphone
            $error["phone"] = 'Le téléphone ne correspond pas au format requis pour un numéro de téléphone francais'; //Message d'erreur numéro de téléphone format
        }
    }
    
    // ?BIRTHDATE
    // Nettoyage des caractères autres que les chiffres & '+' & '-'
    $birthdate = trim(filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_NUMBER_INT));
    
    if (empty($birthdate)) { //Si $birthdate est vide
        $error["birthdate"] = 'La date de naissance n\'est pas renseigné'; //Message d'erreur birthdate
    } elseif (!filter_var($birthdate, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/' . REGEXP_BIRTHDATE . '/')))) { //Sinon si $url ne correspond pas à un format url
        $error["birthdate"] = 'La date de naissance n\'est pas valide'; //Message d'erreur birthdate
    }

    if (!Patient::isNotExist($lastname, $firstname, $email, $birthdate)) {//Si le patient existe déjà en base de données
        flash('patientExist', 'Ce patient existe déjà !', FLASH_DANGER); //Création d'un flash avec le message à afficher 
        $error['patient'] = 'Ce patient existe déjà !';
    }
    // ?No error -> redirect to home page
    if (empty($error)) { // Si aucune erreur après tous les nettoyages et les validations
        
        $patient = new Patient();
        $patient->setLastname($lastname);
        $patient->setFirstname($firstname);
        $patient->setEmail($email);
        if (isset($phone)) {
            $patient->setPhone($phone);
        } else {
            $patient->setPhone('');
        }
        $patient->setBirthdate($birthdate);
        // Vérification que le patient existe pas déja avec la méthode notAlreadyExist()
        // Ajouter du patient à la base de donnée & affecter le résultat de l'exécution de la requête à $result
        $result = $patient->add();
        if (!$result) { //Si une erreur est survenu pendant l'ajout à la base de données
            echo 'message d\'erreur ! (A MODIFIER !)';
        } else { //Si pas d'erreur retour à la page d'Accueil
            flash('patientAdded', 'Patient ajouté avec succès', FLASH_SUCCESS);
            header('location: /Accueil');
            die;
        }
    }
    
    // End if ($_SERVER['REQUEST_METHOD'] == 'POST')
}

} catch (\Throwable $th) {
    include(__DIR__ . '/../views/templates/header.php');
    include(__DIR__ . '/../views/templates/errors.php');
    include(__DIR__ . '/../views/templates/footer.php');
    die;
}

// Appel du header
include(__DIR__ . '/../views/templates/header.php');

// Appel de la view
include(__DIR__ . '/../views/clients/addPatient.php');

// Appel du footer
include(__DIR__ . '/../views/templates/footer.php');
