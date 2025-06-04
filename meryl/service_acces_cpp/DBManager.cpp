#include "DBManager.h"
#include <cppconn/driver.h>
#include <cppconn/prepared_statement.h>
#include <cppconn/resultset.h>
#include <iostream>

//➡️ Inclus les dépendances nécessaires pour :

//SQL avec MySQL Connector/C++ (cppconn/...)

//Affichage des erreurs ou messages (iostream)



DBManager::DBManager() : conn(nullptr) {}
//➡️ Initialise le pointeur conn à nullptr. Cela évite des erreurs si la connexion n’a pas encore été établie.



bool DBManager::connecter() {
    try {
        sql::Driver* driver = get_driver_instance();
        conn = driver->connect("tcp://10.10.0.20", "adminweb", "root");
        conn->setSchema("gestion_stockage");
        return true;
    } catch (sql::SQLException &e) {
        std::cerr << "Erreur de connexion à la BDD" << e.what() << std::endl;
        std::cerr << "Code erreur : " << e.getErrorCode() << std::endl;
        std::cerr << "Etat SQL : " << e.getSQLState() << std::endl;
        return false;
    }
}
//➡️ Se connecte à la base de données. En cas d’erreur, affiche des infos utiles au débogage.

int DBManager::verifierEmpreinte(const std::string& hash) {
    auto stmt = conn->prepareStatement("SELECT id FROM utilisateurs WHERE empreinte_biometrique = UNHEX(?)");
    stmt->setString(1, hash);
    auto res = stmt->executeQuery();
    return res->next() ? res->getInt("id") : -1;
}
//➡️ Cherche un utilisateur correspondant au hash d’empreinte biométrique (converti en binaire avec UNHEX()).
//Retourne l’ID de l’utilisateur s’il existe.
//Sinon retourne -1.

int DBManager::enregistrerAcces(int idUtilisateur) {
    auto stmt = conn->prepareStatement(
        "INSERT INTO acces (utilisateur_id, entree_sortie, date_heure) VALUES (?, 'ENTREE', NOW())"
    );
    stmt->setInt(1, idUtilisateur);
    stmt->execute();

    auto lastIdStmt = conn->prepareStatement("SELECT LAST_INSERT_ID()");
    auto res = lastIdStmt->executeQuery();
    return res->next() ? res->getInt(1) : -1;
}
//➡️ Insère une ligne dans la table acces pour noter l’entrée d’un utilisateur.
//entree_sortie = 'ENTREE'
//La date est mise à l’instant actuel (NOW()).
//Retourne l’ID de l’accès créé.

int DBManager::verifierPositionCapteur(int position) {
    if (!conn) return -1;

    try {
        std::unique_ptr<sql::PreparedStatement> stmt(
            conn->prepareStatement("SELECT id FROM utilisateurs WHERE position_capteur = ?"));
        stmt->setInt(1, position);

        std::unique_ptr<sql::ResultSet> res(stmt->executeQuery());

        if (res->next()) {
            return res->getInt("id");
        }
    } catch (sql::SQLException &e) {
        std::cerr << "Erreur BDD verifierPositionCapteur : " << e.what() << std::endl;
    }

    return -1;
}
//➡️ Vérifie si une position de capteur est déjà associée à un utilisateur.
//Si oui : retourne son ID.
//Sinon : retourne -1.

void DBManager::cloturerAcces(int idAcces) {
    auto stmt = conn->prepareStatement(
        "UPDATE acces SET duree = TIMESTAMPDIFF(SECOND, date_heure, NOW()) WHERE id = ?"
    );
    stmt->setInt(1, idAcces);
    stmt->execute();
}
//➡️ Met à jour la ligne d’accès en ajoutant le temps passé (en secondes).


int DBManager::getDernierAccesOuvert(int idUtilisateur) {
    auto stmt = conn->prepareStatement(
        "SELECT id, TIMESTAMPDIFF(SECOND, date_heure, NOW()) as ecoule "
        "FROM acces WHERE utilisateur_id = ? AND duree IS NULL "
        "ORDER BY date_heure DESC LIMIT 1"
    );
    stmt->setInt(1, idUtilisateur);
    auto res = stmt->executeQuery();

    if (res->next()) {
        int secondsElapsed = res->getInt("ecoule");
        if (secondsElapsed < 120) { // moins de 2 minutes
            return res->getInt("id"); // session en cours = sortie
        } else {
            return -1; // session trop ancienne = on ignore
        }
    }

    return -1;
}
//➡️ Vérifie s’il existe un accès non terminé (sans durée) pour un utilisateur.
//Si l’accès a été ouvert il y a moins de 2 minutes → retourne son ID.
//Sinon, ou s’il n’y en a pas → retourne -1.


DBManager::~DBManager() {
    if (conn) delete conn;
}
//➡️ Libère proprement la mémoire allouée à la connexion SQL.