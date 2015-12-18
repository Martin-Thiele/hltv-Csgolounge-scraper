package csgoscraper;

import java.sql.*;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Scanner;
import java.text.SimpleDateFormat;

public class Database {
    // JDBC driver name and database URL
    static final String JDBC_DRIVER = "com.mysql.jdbc.Driver";
    static final String DB_URL = "jdbc:mysql://<DATABASE_URL>?useServerPrepStmts=false&rewriteBatchedStatements=true";

    // Database credentials
    static final String USER = "USER";
    static final String PASS = "PASS";

    /** Insert into db.Matches
     * @param team_1 - Team id 1
     * @param team_2 - Team id 2
     * @param date   - Formatted date
     * @param time   - Start time
     * @param mapid  - Map
     * @param subcid - Subcompetition id
     * @param hltv   - hltv link
     */
    public static void insert(int team_1, int team_2, String date, String time, int mapid, int subcid, String csgllink, int odds1, int odds2, String hltv){

        if (team_1 == 0) {team_1 = 108;} // Error handling and change to team "TBD"
        if (team_2 == 0) {team_2 = 108;} // ^
        Connection conn = null;
        Statement stmt = null;
        try{
            Class.forName("com.mysql.jdbc.Driver");

            // Open connection
            conn = DriverManager.getConnection(DB_URL, USER, PASS);

            //Start query
            PreparedStatement insert = conn.prepareStatement
                    ("INSERT INTO Matches"
                            + "(tid_1, tid_2, match_date, time, mapid, score_1, score_2, subcid, complete, csglodds1, csglodds2, csgl, hltv) VALUES"
                            + "(?,?,?,?,?,?,?,?,?,?,?,?,?)");

            insert.setInt(1, team_1);
            insert.setInt(2, team_2);
            insert.setString(3, date);
            insert.setString(4, time);
            insert.setInt(5, mapid);
            insert.setInt(6, 0);
            insert.setInt(7, 0);
            insert.setInt(8, subcid);
            insert.setInt(9, 0);
            insert.setInt(10, odds1);
            insert.setInt(11, odds2);
            if(csgllink == null) {
                insert.setNull(12, Types.VARCHAR);
            }
            else{insert.setString(12, csgllink);}
            insert.setString(13, hltv);
            insert.executeUpdate(); // Run query
        }catch(SQLException se){
            //Handle errors for JDBC
            se.printStackTrace();
        }catch(Exception e){
            //Handle errors for Class.forName
            e.printStackTrace();
        }finally{
            //finally block used to close resources
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }// do nothing
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
    }

    /** Update db.Matches on hltv link
     * @param team_1  - Team id 1
     * @param team_2  - Team id 2
     * @param date    - Formatted date
     * @param time    - Start time
     * @param mapid   - Map
     * @param score_1 - Score for team 1
     * @param score_2 - Score for team 2
     * @param subcid  - Subcompetition id
     * @param mid     - Matchid
     */
    public static void update(int team_1, int odds1, int team_2, int odds2, String date, String time, int mapid, int score_1, int score_2, int subcid, String csgllink, int mid, int complete) {
        if (team_1 == 0) {
            team_1 = 108;
        } // Error handling and change to team "TBD"
        if (team_2 == 0) {
            team_2 = 108;
        } // ^

        int tbdcheck = 0;
        if(team_1 == 108 && team_2 == 108){
            tbdcheck = 1;
        }


        if(tbdcheck == 1) {
            Connection conn = null;
            Statement stmt = null;
            try {
                Class.forName("com.mysql.jdbc.Driver");

                // Open connection
                conn = DriverManager.getConnection(DB_URL, USER, PASS);

                //Start query
                PreparedStatement update = conn.prepareStatement
                        ("UPDATE Matches SET match_date = ?, time = ?, mapid = ?,  score_1 = ?,  score_2 = ?,  subcid = ?, complete = ?, csglodds1 = ?, csglodds2 = ?, csgl = ? WHERE mid = ?");

                update.setString(1, date);
                update.setString(2, time);
                update.setInt(3, mapid);
                update.setInt(4, score_1);
                update.setInt(5, score_2);
                update.setInt(6, subcid);
                update.setInt(7, 0);
                update.setInt(8, 0);
                update.setInt(9, 0);
                update.setNull(10, Types.INTEGER);
                update.setInt(11, mid);
                update.executeUpdate(); // Run query
            } catch (SQLException se) {
                //Handle errors for JDBC
                se.printStackTrace();
            } catch (Exception e) {
                //Handle errors for Class.forName
                e.printStackTrace();
            } finally {
                //finally block used to close resources
                try {
                    if (stmt != null)
                        conn.close();
                } catch (SQLException se) {
                }
                try {
                    if (conn != null)
                        conn.close();
                } catch (SQLException se) {
                    se.printStackTrace();
                }
            }
        }
        else if (odds1 != 0 && odds2 != 0 && csgllink != null) {
            Connection conn = null;
            Statement stmt = null;
            try {
                Class.forName("com.mysql.jdbc.Driver");

                // Open connection
                conn = DriverManager.getConnection(DB_URL, USER, PASS);

                //Start query
                PreparedStatement update = conn.prepareStatement
                        ("UPDATE Matches SET tid_1 = ?, tid_2 = ?, match_date = ?, time = ?, mapid = ?,  score_1 = ?,  score_2 = ?,  subcid = ?, csglodds1 = ?, csglodds2 = ?, csgl = ?, complete = ? WHERE mid = ?");

                update.setInt(1, team_1);
                update.setInt(2, team_2);
                update.setString(3, date);
                update.setString(4, time);
                update.setInt(5, mapid);
                update.setInt(6, score_1);
                update.setInt(7, score_2);
                update.setInt(8, subcid);
                update.setInt(9, odds1);
                update.setInt(10, odds2);
                update.setString(11, csgllink);
                update.setInt(12, complete);
                update.setInt(13, mid);
                update.executeUpdate(); // Run query
            } catch (SQLException se) {
                //Handle errors for JDBC
                se.printStackTrace();
            } catch (Exception e) {
                //Handle errors for Class.forName
                e.printStackTrace();
            } finally {
                //finally block used to close resources
                try {
                    if (stmt != null)
                        conn.close();
                } catch (SQLException se) {
                }
                try {
                    if (conn != null)
                        conn.close();
                } catch (SQLException se) {
                    se.printStackTrace();
                }
            }
        }

        else if (odds1 == 0 && odds2 == 0 && csgllink == null) {
            Connection conn = null;
            Statement stmt = null;
            try {
                Class.forName("com.mysql.jdbc.Driver");

                // Open connection
                conn = DriverManager.getConnection(DB_URL, USER, PASS);

                //Start query
                PreparedStatement update = conn.prepareStatement
                        ("UPDATE Matches SET tid_1 = ?, tid_2 = ?, match_date = ?, time = ?, mapid = ?,  score_1 = ?,  score_2 = ?,  subcid = ? WHERE mid = ?");

                update.setInt(1, team_1);
                update.setInt(2, team_2);
                update.setString(3, date);
                update.setString(4, time);
                update.setInt(5, mapid);
                update.setInt(6, score_1);
                update.setInt(7, score_2);
                update.setInt(8, subcid);
                update.setInt(9, mid);
                update.executeUpdate(); // Run query
            } catch (SQLException se) {
                //Handle errors for JDBC
                se.printStackTrace();
            } catch (Exception e) {
                //Handle errors for Class.forName
                e.printStackTrace();
            } finally {
                //finally block used to close resources
                try {
                    if (stmt != null)
                        conn.close();
                } catch (SQLException se) {
                }
                try {
                    if (conn != null)
                        conn.close();
                } catch (SQLException se) {
                    se.printStackTrace();
                }
            }
        }
        else{System.out.println("Unknown update: tbd:" +tbdcheck+ "odds: " +odds1+ ", " +odds2+ ", link: "+csgllink);}

    }

    /**
     *
     * @return ArrayList - List of hltv links
     */

    /*

        HELPER FUNCTIONS

     */

    /** Gives a list of incomplete matches
     * @return ArrayList - Incomplete matches
     */
    public static ArrayList<String> getIncomplete(){
        ArrayList<String> incommatches = new ArrayList<>();
        Connection conn = null;
        Statement stmt = null;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();

            String sql = "SELECT * FROM(select tid_1, tid_2, time, match_date, mid, hltv from Matches WHERE complete = 0 AND hltv != '' ORDER BY mid asc) AS T " +
                    "GROUP BY tid_1, tid_2, time, match_date, hltv ORDER BY mid asc";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                incommatches.add(rs.getString("hltv"));
            }
            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return incommatches;
    }

    /** Converts a hltv img src to a map name
     * @param imgsrc - hltv img for maps
     * @return String - Mapname
     */

    /** Converts a subcomp name to subcid
     * @param subcomp - Subcompetition name
     * @return int - Subcompetition id
     */
    public static int selectSubcid(String subcomp){
        Connection conn = null;
        Statement stmt = null;
        String subfix = subcomp.replaceAll("'", "''");
        int subcid = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "SELECT subcid FROM Subcomp WHERE name ='"+subfix+"' LIMIT 1";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                subcid = rs.getInt("subcid");
            }
            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return subcid;
    }

    public static int selectmid(String hltv){
        Connection conn = null;
        Statement stmt = null;
        int mid = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "SELECT mid FROM Matches WHERE hltv ='"+hltv+"' ORDER BY mid asc LIMIT 1";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                mid = rs.getInt("mid");
            }
            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return mid;
    }


    /** Converts map name to mapid
     * @param mapname - Name of map
     * @return int - mapid
     */
    public static int selectMapid(String mapname){
        Connection conn = null;
        Statement stmt = null;
        int mapid = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "SELECT mapid FROM Maps WHERE name ='"+mapname+"' LIMIT 1";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                mapid = rs.getInt("mapid");
            }
            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return mapid;
    }

    /** Converts team name to teamid
     * @param team - Name of team
     * @return int - Team id
     */
    public static int selectTid(String team){
        Connection conn = null;
        Statement stmt = null;
        int tid = 0;
        int active = 0;
        String teamfix = team.replaceAll("'", "''");
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();

            String sql = "SELECT tid, active FROM Teams WHERE (hltvname ='"+teamfix+"' OR name = '"+teamfix+"' OR csglname = '"+teamfix+"') LIMIT 1";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                tid = rs.getInt("tid");
                active = rs.getInt("tid");
            }
            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
            if(active == 0){markteamactive(tid);}


        }
        return tid;
    }

    // Marks a team active
    public static void markteamactive(int id){
        Connection conn = null;
        Statement stmt = null;

        String sql = "Update Teams SET active = 1 WHERE tid = "+id+" ";
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
                System.out.println("Something happened: markteamactive ");
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
    }


    /** Checks if match already exists in database
     * @param hltv - hltv link
     * @return boolean - whether match exists or not
     */
    public static boolean checkMatchExist(String hltv){
        Connection conn = null;
        Statement stmt = null;
        boolean result = true;
        int check = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "SELECT COUNT(*) AS Count FROM Matches WHERE hltv = '"+hltv+"' LIMIT 1";
            ResultSet rs = stmt.executeQuery(sql);
            while(rs.next()){
                check = rs.getInt("Count");
            }
            if(check == 0){result = false;}
            if(check == 1){result = true;}

            rs.close();
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return result;
    }

    public static int addsubcomp(String subcomp){
        Connection conn = null;
        Statement stmt = null;
        int result = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "INSERT INTO Subcomp(name) VALUES (?)";
            PreparedStatement prest;
            prest = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            prest.setString(1, subcomp);
            prest.executeUpdate();
            ResultSet rs = prest.getGeneratedKeys();
            if(rs.next())
            {
                result = rs.getInt(1);
            }


        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        return result;
    }


    public static void addcomplink(int cid, int subcid){
        Connection conn = null;
        Statement stmt = null;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "INSERT INTO Comp_belongs_to VALUES ("+subcid+", "+cid+")";
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
    }


    /*

        USER COMMANDS

     */



    // Delete match ids
    public static void delete(){
        Connection conn = null;
        Statement stmt = null;
        System.out.println("Enter match ids");
        Scanner scanner = new Scanner(System.in);
        String line = scanner.nextLine();
        String[] tokens = line.split(", ");
        Integer[] numbers = new Integer[tokens.length];

        for (int i=0; i<numbers.length;i++){
            numbers[i] = Integer.parseInt(tokens[i]);}


        String sql = "DELETE FROM Matches WHERE mid = ";
        for (int j = 0; j < numbers.length; j++){
            if(j == 0){
                sql += numbers[0];
            }
            else{
                sql += " OR mid = " + numbers[j];
            }
        }
        System.out.println(sql);
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        System.out.println("matches has been deleted");
    }


    // Deletes matches if complete = 0 and date is less than 3 days of today
    public static void deleteinactive(){
        Connection conn = null;
        Statement stmt = null;
        SimpleDateFormat format1 = new SimpleDateFormat("yyyy-MM-dd");
        Calendar yesterday = Calendar.getInstance();
        yesterday.add(Calendar.DATE, -1);
        Calendar two = Calendar.getInstance();
        two.add(Calendar.DATE, -2);
        Calendar three = Calendar.getInstance();
        three.add(Calendar.DATE, -3);
        String yest = format1.format(yesterday.getTime());
        String twoz = format1.format(two.getTime());
        String threez = format1.format(three.getTime());
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "DELETE FROM Matches WHERE complete = 0 AND (match_date = '"+yest+"' OR match_date = '"+twoz+"'  OR match_date = '"+threez+"') ";
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        System.out.println("inactive matches has been deleted");
    }
    public static void deletetransfer(){
        Connection conn = null;
        Statement stmt = null;
        System.out.println("Enter transfer ids");
        Scanner scanner = new Scanner(System.in);
        String line = scanner.nextLine();
        String[] tokens = line.split(", ");
        Integer[] numbers = new Integer[tokens.length];

        for (int i=0; i<numbers.length;i++){
            numbers[i] = Integer.parseInt(tokens[i]);}


        String sql = "DELETE FROM Playertransfers WHERE id = ";
        for (int j = 0; j < numbers.length; j++){
            if(j == 0){
                sql += numbers[0];
            }
            else{
                sql += " OR id = " + numbers[j];
            }
        }
        System.out.println(sql);
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        System.out.println("transfers has been deleted");
    }



    // Marks all teams inactive if no matches for 90 days
    public static void markinactive(){
        Connection conn = null;
        Statement stmt = null;
        String sql = "UPDATE Teams" +
                "    SET active = 0" +
                "    WHERE tid IN (SELECT tid FROM (SELECT * FROM Teams" +
                "            LEFT JOIN" +
                "            (SELECT DISTINCT(tid_1) FROM Matches WHERE match_date >= CURRENT_DATE - INTERVAL 90 DAY) AS T" +
                "    ON Teams.tid = T.tid_1" +
                "    WHERE T.tid_1 is null) AS dum" +
                "    LEFT JOIN" +
                "            (SELECT DISTINCT(tid_2) FROM Matches WHERE match_date >= CURRENT_DATE - INTERVAL 90 DAY) AS TT" +
                "    ON TT.tid_2 = dum.tid" +
                "    WHERE TT.tid_2 is null AND tid != 40)";
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            stmt.executeUpdate(sql);
        }catch(SQLException se){
            se.printStackTrace();
        }catch(Exception e){
            e.printStackTrace();
        }finally{
            try{
                if(stmt!=null)
                    conn.close();
            }catch(SQLException se){
            }
            try{
                if(conn!=null)
                    conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        System.out.println("Teams have been marked inactive");
    }



}