CXX = g++
SRC = main.cpp DBManager.cpp ClientTCP.cpp
OUT = serviceAcces

INCLUDE = -I$(HOME)/Downloads/mysql-connector-c++-1.1.11-macos10.13-x86-64bit/include \
          -I/opt/homebrew/opt/openssl/include

LIBPATH = -L$(HOME)/Downloads/mysql-connector-c++-1.1.11-macos10.13-x86-64bit/lib \
          -L/opt/homebrew/opt/openssl/lib

LIBS = -lmysqlcppconn -lssl -lcrypto -lwiringPi

all:
	$(CXX) $(SRC) -o $(OUT) $(INCLUDE) $(LIBPATH) $(LIBS)

clean:
	rm -f $(OUT)
