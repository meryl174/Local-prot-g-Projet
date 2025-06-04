#ifndef CLIENTTCP_H
#define CLIENTTCP_H

#include <string>

class ClientTCP {
public:
    ClientTCP(const std::string& ip, int port);
    bool connecter();
    void envoyer(const std::string& message);
    void deconnecter();

private:
    std::string ip;
    int port;
    int sockfd;
};

#endif