package csgoscraper;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.IOException;
import java.util.ArrayList;

import static csgoscraper.Database.getTid;

/**
 * @author Martin "Shrewbi" Thiele
 * @version 1.0.1
 * @since  1.0.1
 */


public class Csglscraper {
    static String userAgent = "useragent";
    static ArrayList<Integer> scrapedodds1 = new ArrayList<>();
    static ArrayList<Integer> scrapedodds2 = new ArrayList<>();
    static ArrayList<String>  scrapedlinks = new ArrayList<>();
    static ArrayList<Integer> scrapedtid1  = new ArrayList<>();
    static ArrayList<Integer> scrapedtid2  = new ArrayList<>();
    static ArrayList<String> scrapedcomps  = new ArrayList<>();
    ArrayList<String> scrapedteam1 = new ArrayList<>();
    ArrayList<String> scrapedteam2 = new ArrayList<>();
    public Csglscraper(){
        //csgolounge scraper
        try {
            // Connect to scrape page
            Document doc = Jsoup.connect("http://csgolounge.com/").userAgent(userAgent).get();
            // Scrape matchlinks
            Elements links = doc.select("div.match a[href]");
            for (Element link : links) {
                String scraped = link.attr("href");
                if(!scraped.contains("predict")){scrapedlinks.add(scraped);} // Remove predictions

            }


            // Scrape teams
            Elements teams = doc.select("div.match div.teamtext b");
            int count = 0;
            for (Element team : teams) {
                String scraped = team.text();
                count++;
                if(count % 2 != 0) {scrapedteam1.add(scraped);}
                else{scrapedteam2.add(scraped);}
            }


            // Scrape odds
            Elements odds = doc.select("div.match div.teamtext i");
            count = 0;
            for (Element odd : odds) {
                String scraped = odd.text();
                String scrapeodds;
                count++;
                switch (scraped.length()) {
                    case 2:  scrapeodds = scraped.substring(0,1); break;
                    case 3:  scrapeodds = scraped.substring(0,2); break;
                    case 4:  scrapeodds = scraped.substring(0,3); break;
                    default: scrapeodds = "0";
                }
                if(count % 2 != 0){scrapedodds1.add(Integer.parseInt(scrapeodds));}
                else{scrapedodds2.add(Integer.parseInt(scrapeodds));}
            }


            // Scrape competition
            Elements comps = doc.select("div.matchheader div.eventm");
            for (Element comp : comps) {
                String scraped = comp.text();
                scrapedcomps.add(scraped);
            }

            // Convert to teamids
            for(int s = 0; s < scrapedteam1.size(); s++){
                int stid1 = getTid(scrapedteam1.get(s), scrapedcomps.get(s));
                int stid2 = getTid(scrapedteam2.get(s), scrapedcomps.get(s));
                scrapedtid1.add(stid1);
                scrapedtid2.add(stid2);
            }

        }

        catch (IOException e) {
            //e.printStackTrace();
            System.out.println(e + " for csgolounge");
        }
    }

    public ArrayList<Integer> gettids1(){
        return scrapedtid1;
    }

    public ArrayList<Integer> gettids2(){
        return scrapedtid2;
    }

    public ArrayList<Integer> getodds1(){
        return scrapedodds1;
    }

    public ArrayList<Integer> getodds2(){
        return scrapedodds2;
    }

    public ArrayList<String> getcomps(){
        return scrapedcomps;
    }

    public ArrayList<String> getlinks(){
        return scrapedlinks;
    }


}
