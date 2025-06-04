#include "DBManager.h"
#include <cppconn/driver.h>
#include <cppconn/prepared_statement.h>
#include <cppconn/resultset.h>
#include <iostream>

DBManager::DBManager() : conn(nullptr) {}

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

int DBManager::verifierEmpreinte(const std::string& hash) {
    auto stmt = conn->prepareStatement("SELECT id FROM utilisateurs WHERE empreinte_biometrique = UNHEX(?)");
    stmt->setString(1, hash);
    auto res = stmt->executeQuery();
    return res->next() ? res->getInt("id") : -1;
}

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

void DBManager::cloturerAcces(int idAcces) {
    auto stmt = conn->prepareStatement(
        "UPDATE acces SET duree = TIMESTAMPDIFF(SECOND, date_heure, NOW()) WHERE id = ?"
    );
    stmt->setInt(1, idAcces);
    stmt->execute();
}

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


DBManager::~DBManager() {
    if (conn) delete conn;
}
