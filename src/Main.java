package csgoscraper;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.IOException;
import java.text.DateFormat;
import java.text.ParsePosition;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;
import java.util.concurrent.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.Scanner;

import static csgoscraper.Database.*;

/**
* @author Martin "Shrewbi" Thiele
* @since  16-12-2015
*/
// TODO: replace scanners with args
// TODO: scrape csgl if link exists, but match not found on front page (Too many matches on their site)
// TODO: paste to csv
// TODO: GUI
// TODO: optimize


public class Main {
    // Scraping settings
    static String userAgent = "useragent";
    static int i = 10; // Scraping timer in minutes

    static int subcide = 0; // Amount of subcid errors
    static int teame = 0; // Amount of team errors
    static int updatecount;
    static int matchcount;
    static ArrayList<Integer> scrapedodds1 = new ArrayList<>();
    static ArrayList<Integer> scrapedodds2 = new ArrayList<>();
    static ArrayList<String>  scrapedlinks = new ArrayList<>();
    static ArrayList<Integer> scrapedtid1  = new ArrayList<>();
    static ArrayList<Integer> scrapedtid2  = new ArrayList<>();
    static int sz;

    // Scrape for matches then insert or update
    public static void getmatches() {
        updatecount = 0;
        matchcount = 0;
        String gettime = getNextTime();

        // Reset variables
        ArrayList<String> scrapedteam1 = new ArrayList<>();
        ArrayList<String> scrapedteam2 = new ArrayList<>();
        ArrayList<String> matchpages   = new ArrayList<>();
        ArrayList<String> incommatches = new ArrayList<>();
        scrapedodds1 = new ArrayList<>();
        scrapedodds2 = new ArrayList<>();
        scrapedtid1  = new ArrayList<>();
        scrapedtid2  = new ArrayList<>();
        scrapedlinks = new ArrayList<>();


        //Hltv scraper
        try {

            // Connect to scrape page
            Document doc = Jsoup.connect("http://www.hltv.org/?pageid=305").userAgent(userAgent).get();
            // Scrape for links in div.center and add to arraylist
            Elements links = doc.select("div.center a");
            for (Element link : links) {
                String href = link.attr("href").substring(0, 15);
                String match = ("http://www.hltv.org" + href);
                matchpages.add(match);
            }
        } catch (IOException e) {
            //e.printStackTrace();
            System.out.println(e + " for hltv");
        }


        //csgolounge scraper
        try {
            // Connect to scrape page
            Document doc = Jsoup.connect("http://www.csgolounge.com").userAgent(userAgent).get();
            // Scrape matchlinks
            Elements links = doc.select("div.match a[href]");
            for (Element link : links) {
                String scraped = link.attr("href");
                if(scraped.contains("predict")){}
                else {
                    scrapedlinks.add(scraped);
                }
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

            // Convert to teamids
            for(int s = 0; s < scrapedteam1.size(); s++){
                int stid1 = selectTid(scrapedteam1.get(s));
                int stid2 = selectTid(scrapedteam2.get(s));
                scrapedtid1.add(stid1);
                scrapedtid2.add(stid2);
            }
            sz = scrapedtid1.size();


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
        }

        catch (IOException e) {
            //e.printStackTrace();
            System.out.println(e + " for csgolounge");
        }


        // Update matches
        incommatches = getIncomplete(); // Get list of incomplete matches
        System.out.println(incommatches.size()+" active matches");
        for (int j = 0; j < incommatches.size(); j++ ){
            scrapepage("Update", incommatches.get(j));
        }

        // Add matches
        for (int i = 0; i < matchpages.size(); i++){
        if (!checkMatchExist(matchpages.get(i))) {
            scrapepage("Insert", matchpages.get(i));}
        }
        System.out.println(matchcount+" new matches added");
        System.out.println(updatecount+" matches updated");
        System.out.println("Team errors: "+ teame);
        System.out.println("Competition errors: "+ subcide);
        System.out.println(gettime);


    }

    /** scrape matchpage, then insert or update
     * @param whatdo - Whether to insert or update row
     * @param page   - hltv matchpage
     */
    public static void scrapepage(String whatdo, String page){
        ArrayList<String> scrapeteams  = new ArrayList<>();
        ArrayList<String> scrapemaps   = new ArrayList<>();
        ArrayList<String> scrapescores = new ArrayList<>();
        String stringdate;
        String stringtime;
        try {
            // Connect to hltv matchpage
            Document matchpage = Jsoup.connect(page).userAgent(userAgent).get();
            // Scrape for teams and add to arraylist
            Elements teams = matchpage.select("div.centerfade span a.nolinkstyle");
            for (Element team : teams) {
                scrapeteams.add(team.text());
            }
            // Errorhandling for unknown team names and empty list
            if(scrapeteams.isEmpty()){scrapeteams.add("TBD");scrapeteams.add("TBD");}
            if(selectTid(scrapeteams.get(0)) == 0){scrapeteams.set(0, "TBD"); teame++;}
            if(selectTid(scrapeteams.get(1)) == 0){scrapeteams.set(1, "TBD"); teame++;}

            // Get date
            Elements sd  = matchpage.select("div[style=padding:5px;] span[style=font-size:14px;]");
            stringdate = sd.text();

            // Get time
            Elements time  = matchpage.select("div[style=padding:5px;] span[style=margin-left:10px;]");
            stringtime= time.text();

            // Get competition
            Elements comp = matchpage.select("div[style=padding:5px;] div[style=text-align:center;font-size: 18px;] a");
            String competition = comp.text();

            // Convert and check competition
            int subcid = selectSubcid(competition);
            if(subcid == 0){
                int checkcomp = frequentcomp(competition); // Check if competition includes name from frequent competition hosts
                if(checkcomp == 0) {
                    subcid = 154; // filler subcompetition id
                }
                else{
                    subcid = checkcomp;
                }
            }

            // Get maps
            Elements maps = matchpage.select("div[style=border: 1px" +
                    " solid darkgray;" +
                    "border-radius: 5px;" +
                    "width:280px;" +
                    "height:28px;" +
                    "margin-bottom:3px;] img");
            for (Element map : maps) {
                scrapemaps.add(map.absUrl("src"));
            }

            // Get best-of scenario
            int bo;
            bo = scrapemaps.size();

            // Get scores
            Elements scores = matchpage.select("div.hotmatchbox[style=margin-top: -7px;" +
                    "font-size: 12px;" +
                    "width:270px;" +
                    "border-top:0;]");
            for (Element score : scores) {
                String[] parts = regexfinder(score.text(), "^[\\d]{1,2}:[\\d]{1,2}").split(":");
                scrapescores.add(parts[0]);
                scrapescores.add(parts[1]);
            }

            // Errorhandling for scores if no scores found for map x
            if(scrapescores.size() < bo * 2){
                while(scrapescores.size() < bo * 2)
                {
                    scrapescores.add("0");
                }
            }

            // Get database variables
            int tid1 = selectTid(scrapeteams.get(0)); // convert teamid to teamname
            int tid2 = selectTid(scrapeteams.get(1)); // ^
            stringdate = formatdate(stringdate);      // Formatdate to proper database format

            // Get odds
            int odds1 = 0;
            int odds2 = 0;
            String csgllink = null;
            for(int k = 0; k < sz; k++){  // Match given csgl team id with database team ids
                if(tid1 == scrapedtid1.get(k) && tid2 == scrapedtid2.get(k)){        // If team 1 = team 1
                    odds1 = scrapedodds1.get(k);
                    odds2 = scrapedodds2.get(k);
                    csgllink = "http://csgolounge.com/".concat(scrapedlinks.get(k));
                }
                else if(tid2 == scrapedtid1.get(k) && tid1 == scrapedtid2.get(k)) {  // If team 2 = team 1
                    odds2 = scrapedodds1.get(k);
                    odds1 = scrapedodds2.get(k);
                    csgllink = "http://csgolounge.com/".concat(scrapedlinks.get(k));
                }

            }
            if(bo > 0) {
                int[] mapid   = new int[scrapemaps.size()];
                int[] score_1 = new int[scrapemaps.size()];
                int[] score_2 = new int[scrapemaps.size()];
                for(int k = 0; k < scrapemaps.size(); k++){
                    mapid[k] = (selectMapid(selectMapname(scrapemaps.get(k))));
                    if (k > 0) { // if not first match
                        score_1[k] = Integer.parseInt(scrapescores.get(2 * k));
                        score_2[k] = Integer.parseInt(scrapescores.get(2 * k + 1));

                    } else { // If first match
                        score_1[k] = Integer.parseInt(scrapescores.get(0));
                        score_2[k] = Integer.parseInt(scrapescores.get(1));
                    }
                }
                if (whatdo.equals("Insert")) {
                        insert(tid1,
                                tid2,
                                score_1,
                                score_2,
                                mapid,
                                stringdate,
                                stringtime,
                                subcid,
                                csgllink,
                                odds1,
                                odds2,
                                page
                        );
                    System.out.println("Match added: " + scrapeteams.get(0) + " - " + scrapeteams.get(1));
                    matchcount++;
                }
                // Update match
                else {
                    // Mark matches complete - handling
                    int mid = selectmid(page);
                    if (mid != 0) {


                            update(
                                    tid1,
                                    odds1,
                                    tid2,
                                    score_1,
                                    score_2,
                                    mapid,
                                    odds2,
                                    stringdate,
                                    stringtime,
                                    subcid,
                                    csgllink,
                                    mid
                            );
                            mid++;


                        System.out.println("Updated: " + scrapeteams.get(0) + " vs " + scrapeteams.get(1));
                        updatecount++;
                    }
                    else{System.out.println("MID ERROR");} // Something bad happened
                }

            }
        }catch (IOException e) {
           // e.printStackTrace();
            System.out.println(e + " for hltv matchpage");
        }
    }

    /** formats date to database format
     * @param inputdate - any date format
     * @return String   - formatted date
     */
    public static String formatdate(String inputdate) {
        inputdate = inputdate.replaceAll("( of)", "");
        String returndate = null;
        Date parsedDate;
        String[] formats = {"d'st' MMMM yyyy","d'nd' MMMM yyyy","d'rd' MMMM yyyy","d'th' MMMM yyyy"};
        ParsePosition position = new ParsePosition(0);
        for (String format : formats)
        {
            position.setIndex(0);
            position.setErrorIndex(-1);
            // no ParseException but a null return instead
            parsedDate = new SimpleDateFormat(format, Locale.ENGLISH).parse(inputdate, position);
            if (parsedDate != null) {
                SimpleDateFormat date_formatter = new SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH);
               returndate = date_formatter.format(parsedDate);
            }
        }
        return returndate;
    }

    /** print next scrape time
     * @return String - time + i minutes
     */
    public static String getNextTime(){
        DateFormat nextformat = new SimpleDateFormat("HH:mm:ss");
        Calendar next = Calendar.getInstance();
        next.add(Calendar.MINUTE, i);
        String time = "Next scrape at: "+nextformat.format(next.getTime());
        return time;
    }

    /**
     *
     * @param text - Text to find pattern in
     * @param tofindregex - regex pattern
     * @return String - Found text fitting regex pattern
     */
    public static String regexfinder(String text, String tofindregex){
        Pattern pat = Pattern.compile(tofindregex); // Regex pattern
        Matcher match = pat.matcher(text);
        String result = null;
        while (match.find()) {
            result = match.group();
        }
        return result;
    }

    public static String selectMapname(String imgsrc){
        String mapname = "TBA";
        switch (imgsrc){
        case "http://static.hltv.org//images/hotmatch/default.png":         mapname = "Unplayed"; break;
            case "http://static.hltv.org//images/hotmatch/tba.png":         mapname = "TBA"; break;
            case "http://static.hltv.org//images/hotmatch/mirage.png":      mapname = "de_mirage"; break;
            case "http://static.hltv.org//images/hotmatch/cache.png":       mapname = "de_cache"; break;
            case "http://static.hltv.org//images/hotmatch/cobblestone.png": mapname = "de_cbble"; break;
            case "http://static.hltv.org//images/hotmatch/dust2.png":       mapname = "de_dust_2"; break;
            case "http://static.hltv.org//images/hotmatch/nuke.png":        mapname = "de_nuke"; break;
            case "http://static.hltv.org//images/hotmatch/season.png":      mapname = "de_season"; break;
            case "http://static.hltv.org//images/hotmatch/inferno.png":     mapname = "de_inferno"; break;
            case "http://static.hltv.org//images/hotmatch/overpass.png":    mapname = "de_overpass"; break;
            case "http://static.hltv.org//images/hotmatch/train.png":       mapname = "de_train"; break;
        }
        return mapname;
    }


    /** Automatically add and link frequent competitions
    *   first addsubcomp and store the subcid in tmp.
    *   then link the two in a belongs_to table with competiton id and the tmp subcompetition id
    */
    public static int frequentcomp(String comp){
        int tmp = 0;
        if(comp.contains("QuickShot")){        tmp = addsubcomp(comp); addcomplink(72, tmp);} 
        if(comp.contains("ESL")){              tmp = addsubcomp(comp); addcomplink(6,  tmp);}
        if(comp.contains("ESL ESEA")){         tmp = addsubcomp(comp); addcomplink(45, tmp);} // Overwrites tmp if ESL
        if(comp.contains("FACEIT")){           tmp = addsubcomp(comp); addcomplink(11, tmp);}
        if(comp.contains("CEVO")){             tmp = addsubcomp(comp); addcomplink(2,  tmp);}
        if(comp.contains("DreamHack")){        tmp = addsubcomp(comp); addcomplink(3,  tmp);}
        if(comp.contains("ESWC")){             tmp = addsubcomp(comp); addcomplink(50, tmp);}
        if(comp.contains("D!ngIT")){           tmp = addsubcomp(comp); addcomplink(40, tmp);}
        if(comp.contains("ASUS")){             tmp = addsubcomp(comp); addcomplink(9,  tmp);}
        if(comp.contains("Fragbite")){         tmp = addsubcomp(comp); addcomplink(12, tmp);}
        if(comp.contains("Gfinity")){          tmp = addsubcomp(comp); addcomplink(23, tmp);}
        if(comp.contains("RGN")){              tmp = addsubcomp(comp); addcomplink(21, tmp);}
        if(comp.contains("StarSeries")){       tmp = addsubcomp(comp); addcomplink(19, tmp);}
        if(comp.contains("Uprise Champions")){ tmp = addsubcomp(comp); addcomplink(25, tmp);}
        if(comp.contains("CS Select")){        tmp = addsubcomp(comp); addcomplink(30, tmp);}
        if(comp.contains("99Damage")){         tmp = addsubcomp(comp); addcomplink(37, tmp);}
        if(comp.contains("Alientech")){        tmp = addsubcomp(comp); addcomplink(83, tmp);}
        if(comp.contains("PGL")){              tmp = addsubcomp(comp); addcomplink(18, tmp);}
        if(comp.contains("IEM")){              tmp = addsubcomp(comp); addcomplink(75, tmp);}
        return tmp;

    }


    // Runner that runs the program
    public static Runnable runner = new Runnable() {
        public void run() {
            long start_time = System.currentTimeMillis();
            teame   = 0; // Reset team errors
            subcide = 0; // Reset competition errors
            getmatches();

            // Start commands
            long elapsed = 0;
            do {
                elapsed =  i * 60000 - (System.currentTimeMillis() - start_time) - 5000; // Run for i minutes - 5 seconds, then lock commands
                System.out.print("Enter command:");
                    ExecutorService executor = Executors.newSingleThreadExecutor();
                    Future<String> future = executor.submit(new Task());
                    try {
                        future.get(elapsed, TimeUnit.MILLISECONDS);
                        future.cancel(true);
                        executor.shutdownNow();
                    } catch (TimeoutException e) {
                        future.cancel(true);
                        executor.shutdownNow();
                        System.out.print(" Locked\n");
                        break;
                    } catch (InterruptedException e) {
                        e.printStackTrace();
                        future.cancel(true);
                        break;
                    } catch (ExecutionException e) {
                        e.printStackTrace();
                        future.cancel(true);
                        break;
                    }
                    executor.shutdownNow();
                    future.cancel(true);
            }
            while(elapsed > 5000);

        }
    };
    public static void main(String[] args) {
        // Start runner every i minute
        ScheduledExecutorService executor = Executors.newScheduledThreadPool(1);
        executor.scheduleAtFixedRate(runner, 0, i, TimeUnit.MINUTES);

    }
}
