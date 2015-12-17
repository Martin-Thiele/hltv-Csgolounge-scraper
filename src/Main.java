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
    static ArrayList<String> scrapedlinks = new ArrayList<>();
    static ArrayList<Integer> scrapedtid1 = new ArrayList<>();
    static ArrayList<Integer> scrapedtid2 = new ArrayList<>();
    static int sz;

    // Scrape for matches then insert or update



    public static void getmatches() {
        updatecount = 0;
        matchcount = 0;
        String gettime = getNextTime();

        // Reset arraylists
        ArrayList<String> scrapedteam1 = new ArrayList<>();
        ArrayList<String> scrapedteam2 = new ArrayList<>();
        ArrayList<String> matchpages = new ArrayList<>();
        ArrayList<String> incommatches = new ArrayList<>();
        scrapedodds1 = new ArrayList<>();
        scrapedodds2 = new ArrayList<>();
        scrapedtid1 = new ArrayList<>();
        scrapedtid2 = new ArrayList<>();
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
                String scrapeodds = "0";
                count++;
                switch (scraped.length()) {
                    case 2: scrapeodds = scraped.substring(0,1); break;
                    case 3: scrapeodds = scraped.substring(0,2); break;
                    case 4: scrapeodds = scraped.substring(0,3); break;
                    default: scrapeodds = "0";
                }
                if(count % 2 != 0) {scrapedodds1.add(Integer.parseInt(scrapeodds));}
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
        int counterrors = teame+subcide;
        System.out.println("");
        System.out.println(matchcount+" new matches added");
        System.out.println(updatecount+" matches updated");
        System.out.println("Errors: " + counterrors);
        System.out.println("Team: "+ teame);
        System.out.println("Competition errors: "+ subcide);
        System.out.println(gettime);


    }

    /** scrape matchpage, then insert or update
     * @param whatdo - Whether to insert or update row
     * @param page   - hltv matchpage
     */
    public static void scrapepage(String whatdo, String page){
        ArrayList<String> scrapeteams= new ArrayList<>();
        ArrayList<String> scrapemaps= new ArrayList<>();
        ArrayList<String> scrapescores= new ArrayList<>();
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


            int subcid = selectSubcid(competition);
            if(subcid == 0){
                int checkcomp = frequentcomp(competition);
                if(checkcomp == 0) {
                    subcid = 71; // filler
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

            int bo = 0;
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
            int tid1 = selectTid(scrapeteams.get(0)); // Get team id instead of teamname
            int tid2 = selectTid(scrapeteams.get(1)); // ^
            stringdate = formatdate(stringdate); // Formatdate to proper database format
            // Get odds
            int odds1 = 0;
            int odds2 = 0;
            String csgllink = null;
            for(int k = 0; k < sz; k++){
                if(tid1 == scrapedtid1.get(k) && tid2 == scrapedtid2.get(k)){
                    odds1 = scrapedodds1.get(k);
                    odds2 = scrapedodds2.get(k);
                    csgllink = "http://csgolounge.com/".concat(scrapedlinks.get(k));
                }
                else if(tid2 == scrapedtid1.get(k) && tid1 == scrapedtid2.get(k)) {
                    odds2 = scrapedodds1.get(k);
                    odds1 = scrapedodds2.get(k);
                    csgllink = "http://csgolounge.com/".concat(scrapedlinks.get(k));
                }

            }
            if(bo != 0 && bo > 0 && bo < 8) {
                // Insert match
                if (whatdo.equals("Insert")) {
                    for (int k = 0; k < bo; k++) {
                        insert(tid1,
                                tid2,
                                stringdate,
                                stringtime,
                                selectMapid(selectMapname(scrapemaps.get(k))),
                                subcid,
                                csgllink,
                                odds1,
                                odds2,
                                page
                        );
                    }
                    System.out.println("Match added: " + scrapeteams.get(0) + " - " + scrapeteams.get(1));
                    matchcount++;
                }
                // Update match
                else {
                    int mid = selectmid(page);
                    if (mid != 0) {
                        int t1wins = 0;
                        int t2wins = 0;
                        for (int k = 0; k < bo; k++) {
                            int score1;
                            int score2;
                            if (k > 0) { // if not first match
                                score1 = Integer.parseInt(scrapescores.get(2 * k));
                                score2 = Integer.parseInt(scrapescores.get(2 * k + 1));

                            } else { // If first match
                                score1 = Integer.parseInt(scrapescores.get(0));
                                score2 = Integer.parseInt(scrapescores.get(1));
                            }
                            int mapid = selectMapid(selectMapname(scrapemaps.get(k)));
                            int complete = 0;
                            if((score1 > score2 && score1 > 14) || (score1 == 1 && score2 == 0 && mapid == 10 )){
                                t1wins++;
                                complete = 1;
                            }
                            if((score2 > score1 && score2 > 14) || (score1 == 2 && score2 == 1 && mapid == 10)){
                                t2wins++;
                                complete = 1;
                            }
                            if(bo == 3 && (t1wins == 2 || t2wins == 2)) {complete = 1;}

                            update(
                                    tid1,
                                    odds1,
                                    tid2,
                                    odds2,
                                    stringdate,
                                    stringtime,
                                    mapid,
                                    score1,
                                    score2,
                                    subcid,
                                    csgllink,
                                    mid,
                                    complete
                            );
                            mid++;
                        }

                        System.out.println("Updated: " + scrapeteams.get(0) + " vs " + scrapeteams.get(1));
                        updatecount++;
                    }
                    else{System.out.println("MID ERROR");}
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
     * @return String - time + 10 minutes
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


    // Automatically add and link frequent competitions
    public static int frequentcomp(String comp){
        int tmp = 0;
        if(comp.contains("QuickShot")){tmp = addsubcomp(comp); addcomplink(72, tmp);}
        if(comp.contains("ESL")){      tmp = addsubcomp(comp); addcomplink(6,  tmp);}
        if(comp.contains("ESL ESEA")){ tmp = addsubcomp(comp); addcomplink(45, tmp);}
        if(comp.contains("FACEIT")){   tmp = addsubcomp(comp); addcomplink(11, tmp);}
        if(comp.contains("CEVO")){     tmp = addsubcomp(comp); addcomplink(2,  tmp);}
        if(comp.contains("DreamHack")){tmp = addsubcomp(comp); addcomplink(3,  tmp);}
        if(comp.contains("ESWC")){     tmp = addsubcomp(comp); addcomplink(50, tmp);}
        if(comp.contains("D!ngIT")){   tmp = addsubcomp(comp); addcomplink(40, tmp);}
        return tmp;

    }




    // Runner that runs the program
    public static Runnable runner = new Runnable() {
        public void run() {
            long start_time = System.currentTimeMillis();
            teame = 0; // Reset team errors
            subcide = 0; // Reset competition errors
            getmatches();
            long elapsed = 0;
            do {
                elapsed =  600000 - (System.currentTimeMillis() - start_time) - 10000;
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
