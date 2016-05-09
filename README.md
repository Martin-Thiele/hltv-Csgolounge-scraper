# hltv & csgolounge scraper
####Update: The project will be developed further by another developer. But because of the reasons stated below, a new website will be deployed using this bot.
####This project is no longer active since i have been banned from hltv. I will no longer keep it updated. As a last commit i have also added the webpage i created, as well as all the data i had gathered and database structure.




A scraper for hltv.org and csgolounge.com, written for my project: www.brintos.dk/csgo



This is a scraper written in java with the purpose of scraping latest matches and results from hltv.org, 
as well as gathering odds from csgolounge.com. These results are then added to a database consisting of CS:GO players, teams, maps,
matches, and competitions




# Install
1. in order to take full effect of this scraper, you'll need a database structure to match. A copy of my structure can be found [here](http://brintos.dk/csgo/database_structure.pdf)
2. Replace database information in database.java marked with "DATABASE_URL", "USERNAME", "PASSWORD"
3. Replace userAgent in Main.java
4. In order for the website code to work, "php5-mysqlnd" needs to be installed.

# Dependencies
- JDBC driver
- JSoup
Both libraries are included and can be found [here](https://github.com/Shrewbi/hltv-Csgolounge-scraper/tree/master/brinbot/lib/)
