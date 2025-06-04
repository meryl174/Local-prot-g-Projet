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

//Constructeur DBManager()

//Initialise la classe, notamment le pointeur conn (dans le .cpp il est mis à nullptr).

//Destructeur ~DBManager()

//Libère la connexion à la base de données proprement.

//bool connecter()

//Permet d’établir la connexion à la base de données MySQL.

//int verifierEmpreinte(const std::string& hash)

//Cherche un utilisateur avec une empreinte biométrique donnée (hash).

//Retourne l’ID utilisateur si trouvé, sinon -1.

//int enregistrerAcces(int idUtilisateur)

//Enregistre une entrée (accès) pour un utilisateur donné.

//Retourne l’ID de l’accès créé.

//int verifierPositionCapteur(int position)

//Vérifie si une position de capteur est associée à un utilisateur.

//Retourne l’ID utilisateur associé ou -1.

//void cloturerAcces(int idAcces)

//Met à jour l’enregistrement d’accès pour indiquer la durée de l’accès (sortie).

//int getDernierAccesOuvert(int idUtilisateur)

//Retourne l’ID du dernier accès non clôturé pour un utilisateur, si ce dernier a moins de 2 minutes.

//Sinon, retourne -1.

//Membre privé
//sql::Connection* conn

//Pointeur vers la connexion MySQL.

//Utilisé dans le .cpp pour toutes les opérations SQL.