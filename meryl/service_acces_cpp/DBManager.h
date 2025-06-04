#ifndef DBMANAGER_H
#define DBMANAGER_H

#include <string>
#include <cppconn/connection.h>

class DBManager {
public:
    DBManager();
    ~DBManager();

    bool connecter();
    int verifierEmpreinte(const std::string& hash);
    int enregistrerAcces(int idUtilisateur);
    int verifierPositionCapteur(int position);
    void cloturerAcces(int idAcces);

    int getDernierAccesOuvert(int idUtilisateur);  // ➕ Ajouté

private:
    sql::Connection* conn;
};

#endif
