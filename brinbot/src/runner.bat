:: Runner for the bot. Compiles and runs main with the correct libraries. This should work on linux as well (not tested)

javac -cp .;"../lib/mysql-connector-java-5.1.27.jar";"../lib/jsoup-1.9.1.jar"; csgoscraper/Database.java
javac -cp .;"../lib/mysql-connector-java-5.1.27.jar";"../lib/jsoup-1.9.1.jar"; csgoscraper/Csglscraper.java
javac -cp .;"../lib/mysql-connector-java-5.1.27.jar";"../lib/jsoup-1.9.1.jar"; csgoscraper/Main.java
 
java -cp .;"../lib/mysql-connector-java-5.1.27.jar";"../lib/jsoup-1.9.1.jar"; csgoscraper/Main
