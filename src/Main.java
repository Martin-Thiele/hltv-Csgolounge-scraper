package csgoscraper;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.IOException;
import java.text.ParsePosition;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import static csgoscraper.Database.*;

/**
 * @author Martin "Shrewbi" Thiele
 * @version 1.0.1
 * @since  1.0.1
*/
// TODO: scrape csgl if link exists, but match not found on front page (Too many matches on their site)
// TODO: GUI
// TODO: optimize


public class Main {
    // Scraping settings
    static String userAgent = "useragent";

    static int subcide = 0; // Amount of subcid errors
    static int teame = 0; // Amount of team errors
    static int updatecount;
    static int matchcount;
    static ArrayList<Integer> scrapedodds1 = new ArrayList<>();
    static ArrayList<Integer> scrapedodds2 = new ArrayList<>();
    static ArrayList<String>  scrapedlinks = new ArrayList<>();
    static ArrayList<Integer> scrapedtid1  = new ArrayList<>();
    static ArrayList<Integer> scrapedtid2  = new ArrayList<>();
    static ArrayList<String> scrapedcomps  = new ArrayList<>();
    static ArrayList<String>  usedlinks    = new ArrayList<>();

    // Scrape for matches then insert or update
    public static void getmatches() {
        updatecount = 0; // How many matches got updated
        matchcount = 0;  // How many matches got added




        ArrayList<String> matchpages   = new ArrayList<>();

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
            e.printStackTrace();
            System.out.println(e + " for hltv");
        }


        // Update matches
        ArrayList<String> incommatches = getIncomplete(); // Get list of incomplete matches
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
            int subcid = getSubcid(competition);

            // Scrape for teams and add to arraylist
            Elements teams = matchpage.select("div.centerfade span a.nolinkstyle");
            for (Element team : teams) {
                scrapeteams.add(team.text());
            }

            if(scrapeteams.isEmpty()){scrapeteams.add("TBD");scrapeteams.add("TBD");}
            if(getTid(scrapeteams.get(0), null) == 0){scrapeteams.set(0, "TBD"); teame++;}
            if(getTid(scrapeteams.get(1), null) == 0){scrapeteams.set(1, "TBD"); teame++;}


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
            int tid1 = getTid(scrapeteams.get(0), competition); // convert teamid to teamname
            int tid2 = getTid(scrapeteams.get(1), competition); // ^
            stringdate = formatdate(stringdate);                // Formatdate to proper database format

            // Errorhandling for unknown team names and empty list
            if(scrapeteams.isEmpty()){scrapeteams.add("TBD");scrapeteams.add("TBD");}
            if(tid1 == 0){scrapeteams.set(0, "TBD"); teame++;}
            if(tid2 == 0){scrapeteams.set(1, "TBD"); teame++;}

            // Get odds
            int odds1 = 0;
            int odds2 = 0;
            String csgllink = null;
            int sz = scrapedtid1.size();
            for(int k = 0; k < sz; k++){  // Match given csgl team id with database team ids
                if(tid1 == scrapedtid1.get(k) && tid2 == scrapedtid2.get(k)&& !(usedlinks.contains(scrapedlinks.get(k)))){        // If team 1 = team 1
                        odds1 = scrapedodds1.get(k);
                        odds2 = scrapedodds2.get(k);
                        String tmp = scrapedlinks.get(k);
                        csgllink = "http://csgolounge.com/".concat(tmp);
                        usedlinks.add(tmp);
                        break;
                }
                else if(tid2 == scrapedtid1.get(k) && tid1 == scrapedtid2.get(k) && !(usedlinks.contains(scrapedlinks.get(k)))) {  // If team 2 = team 1
                        odds2 = scrapedodds1.get(k);
                        odds1 = scrapedodds2.get(k);
                        String tmp = scrapedlinks.get(k);
                        csgllink = "http://csgolounge.com/".concat(tmp);
                        usedlinks.add(tmp);
                        break;
                }
            }

                int[] mapid   = new int[scrapemaps.size()];
                int[] score_1 = new int[scrapemaps.size()];
                int[] score_2 = new int[scrapemaps.size()];
                for(int k = 0; k < scrapemaps.size(); k++){
                    mapid[k] = selectMapname(scrapemaps.get(k));
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
                    if(mid == 0){System.out.println("error: match id is zero"); return;}
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
                        System.out.println("Updated: " + scrapeteams.get(0) + " vs " + scrapeteams.get(1));
                        updatecount++;

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

    /**
     *
     * @param imgsrc hltv mapimage
     * @return mapid
     */
    public static Integer selectMapname(String imgsrc){
        int mapname = 0;
        switch (imgsrc){
        case "http://static.hltv.org//images/hotmatch/default.png":         mapname = 10; break;
            case "http://static.hltv.org//images/hotmatch/tba.png":         mapname = 9; break;
            case "http://static.hltv.org//images/hotmatch/mirage.png":      mapname = 6; break;
            case "http://static.hltv.org//images/hotmatch/cache.png":       mapname = 7; break;
            case "http://static.hltv.org//images/hotmatch/cobblestone.png": mapname = 5; break;
            case "http://static.hltv.org//images/hotmatch/dust2.png":       mapname = 2; break;
            case "http://static.hltv.org//images/hotmatch/nuke.png":        mapname = 1; break;
            case "http://static.hltv.org//images/hotmatch/season.png":      mapname = 4; break;
            case "http://static.hltv.org//images/hotmatch/inferno.png":     mapname = 3; break;
            case "http://static.hltv.org//images/hotmatch/overpass.png":    mapname = 8; break;
            case "http://static.hltv.org//images/hotmatch/train.png":       mapname = 11; break;
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

    public static void main(String[] args) {
        Csglscraper derp = new Csglscraper();
        scrapedtid1 = derp.gettids1();
        scrapedtid2 = derp.gettids2();
        scrapedodds1 = derp.getodds1();
        scrapedodds2 = derp.getodds2();
        scrapedcomps = derp.getcomps();
        scrapedlinks = derp.getlinks();
        getmatches();
    }
}
