package csgoscraper;

import java.sql.*;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Scanner;
import java.text.SimpleDateFormat;

import static csgoscraper.Main.*;

/**
* @author Martin "Shrewbi" Thiele
* @since  16-12-2015
*/
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
     * @param subcid - Subcompetition id
     * @param hltv   - hltv link
     */
    public static void insert(int team_1, int team_2, int[] score_1, int[] score_2, int[] mapid, String date, String time, int subcid, String csgllink, int odds1, int odds2, String hltv){

        if (team_1 == 0) {team_1 = 108;} // Error handling and change to team "TBD"
        if (team_2 == 0) {team_2 = 108;} // ^
        Connection conn = null;
        Statement stmt = null;
        int mid = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");

            // Open connection
            conn = DriverManager.getConnection(DB_URL, USER, PASS);

            //Start query
            stmt = conn.createStatement();
            String sql = "INSERT INTO Matches2"
                       + "(tid_1, tid_2, match_date, match_time, subcid, complete, csglodds1, csglodds2, csgl, hltv) VALUES"
                       + "(?,?,?,?,?,?,?,?,?,?)";
            PreparedStatement prest;
            prest = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);

            prest.setInt(1, team_1);
            prest.setInt(2, team_2);
            prest.setString(3, date);
            prest.setString(4, time);
            prest.setInt(5, subcid);
            prest.setInt(6, 0);
            prest.setInt(7, odds1);
            prest.setInt(8, odds2);
            if(csgllink == null) {
                prest.setNull(9, Types.VARCHAR);
            }
            else{prest.setString(9, csgllink);}
            prest.setString(10, hltv);
            prest.executeUpdate(); // Run query
            ResultSet rs = prest.getGeneratedKeys();

            if(rs.next())
            {
                mid = rs.getInt(1);
            }

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
        updatemaps("insert", mid, score_1, score_2, mapid);
    }

    /** Update db.Matches on hltv link
     * @param team_1  - Team id 1
     * @param team_2  - Team id 2
     * @param date    - Formatted date
     * @param time    - Start time
     * @param subcid  - Subcompetition id
     * @param mid     - Matchid
     */
    public static void update(int team_1, int odds1, int team_2, int[] score_1, int[] score_2, int[] mapid, int odds2, String date, String time, int subcid, String csgllink, int mid) {
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
        int t1win = 0;
        int t2win = 0;
        int complete = 0;
        for(int i = 0; i < mapid.length; i++){
            if(score_1[i] > score_2[i] && (score_1[i] > 15 || mapid[i] == 10)){t1win++;}
            if(score_1[i] < score_2[i] && (score_2[i] > 15 || mapid[i] == 10)){t2win++;}
        }
        if(mapid.length == 1){
            if(t1win+t2win == 1)        {complete = 1;}
        }
        if(mapid.length == 2){
            if(t1win+t2win == 2)        {complete = 1;}
        }
        if(mapid.length == 3){
            if(t1win == 2 || t2win == 2){complete = 1;}
        }
        if(mapid.length == 5){
            if(t1win == 3 || t2win == 3){complete = 1;}
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
                        ("UPDATE Matches2 SET match_date = ?, match_time = ?, subcid = ?, complete = ?, csglodds1 = ?, csglodds2 = ?, csgl = ? WHERE mid = ?");

                update.setString(1, date);
                update.setString(2, time);
                update.setInt(3, subcid);
                update.setInt(4, 0);
                update.setInt(5, 0);
                update.setInt(6, 0);
                update.setNull(7, Types.INTEGER);
                update.setInt(8, mid);
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
        else if (odds1 != 0 && odds2 != 0 && csgllink != null && subcid != 154) {
            Connection conn = null;
            Statement stmt = null;
            try {
                Class.forName("com.mysql.jdbc.Driver");

                // Open connection
                conn = DriverManager.getConnection(DB_URL, USER, PASS);

                //Start query
                PreparedStatement update = conn.prepareStatement
                     ("UPDATE Matches2 SET tid_1 = ?, tid_2 = ?, match_date = ?, match_time = ?, " +
                      "subcid = ?, csglodds1 = ?, csglodds2 = ?, csgl = ?, complete = ? WHERE mid = ?");

                update.setInt(1, team_1);
                update.setInt(2, team_2);
                update.setString(3, date);
                update.setString(4, time);
                update.setInt(5, subcid);
                update.setInt(6, odds1);
                update.setInt(7, odds2);
                update.setString(8, csgllink);
                update.setInt(9, complete);
                update.setInt(10, mid);
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
                        ("UPDATE Matches2 SET tid_1 = ?, tid_2 = ?, match_date = ?, match_time = ?,  subcid = ? WHERE mid = ?");

                update.setInt(1, team_1);
                update.setInt(2, team_2);
                update.setString(3, date);
                update.setString(4, time);
                update.setInt(5, subcid);
                update.setInt(6, mid);
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
        else{System.out.println("wrong odds: "+csgllink+ " ("+odds1+"% - "+odds2+"%)");}
        updatemaps("update", mid, score_1, score_2, mapid);
    }

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

            String sql = "SELECT * FROM Matches2 WHERE complete = 0 AND hltv != '' ORDER BY mid asc";
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


    /** Converts a hltv link to match id
     * @param hltv - hltv link
     * @return int - match id
     */
    public static int selectmid(String hltv){
        Connection conn = null;
        Statement stmt = null;
        int mid = 0;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "SELECT mid FROM Matches2 WHERE hltv ='"+hltv+"' LIMIT 1";
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
                active = rs.getInt("active");
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
    // Marks a team inactive
    public static void markteaminactive(int id){
        Connection conn = null;
        Statement stmt = null;

        String sql = "Update Teams SET active = 0 WHERE tid = "+id+" ";
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
                System.out.println("Something happened: markteaminactive ");
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
            String sql = "SELECT COUNT(*) AS Count FROM Matches2 WHERE hltv = '"+hltv+"' LIMIT 1";
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
    /** add a subcompetition to the database
     * @param subcomp - name of subcomp
     * @return int - the returned subcompetition id
     */
    public static int addsubcomp(String subcomp){
        Connection conn = null;
        Statement stmt = null;
        int subcid = 0;
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
                subcid = rs.getInt(1);
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
        return subcid;
    }

    /** link a subcompetition to its
     * respective competition in the Comp_belongs_to table
     * @param cid - competition id
     * @param subcid - Subcompetition id
     */
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

    public static void updatemaps(String whatdo, int mid, int[] score_1, int[] score_2, int[] mapid){
        if(whatdo.equals("update")) {
            Connection conn = null;
            Statement stmt = null;
            ArrayList<Integer> pmapid = new ArrayList<>();
            try{
                Class.forName("com.mysql.jdbc.Driver");
                conn = DriverManager.getConnection(DB_URL, USER, PASS);
                stmt = conn.createStatement();
                String sql = "SELECT pmapid FROM Map_belongs_to WHERE mid = "+mid+"";
                ResultSet rs = stmt.executeQuery(sql);
                while(rs.next()){
                    pmapid.add(rs.getInt("pmapid"));
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
            for (int i = 0; i < pmapid.size(); i++) {
                try {
                    Class.forName("com.mysql.jdbc.Driver");
                    conn = DriverManager.getConnection(DB_URL, USER, PASS);
                    stmt = conn.createStatement();
                    PreparedStatement update = conn.prepareStatement
                            ("UPDATE Playedmaps SET mapid = ?, score_1 = ?, score_2 = ? WHERE pmapid = ?");

                    update.setInt(1, mapid[i]);
                    update.setInt(2, score_1[i]);
                    update.setInt(3, score_2[i]);
                    update.setInt(4, pmapid.get(i));
                    update.executeUpdate(); // Run query

                } catch (SQLException se) {
                    se.printStackTrace();
                } catch (Exception e) {
                    e.printStackTrace();
                } finally {
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
        }
        else{
            ArrayList<Integer> pmapid = new ArrayList<>();
            for(int i = 0; i < mapid.length; i++) {
                    Connection conn = null;
                    Statement stmt = null;
                    try {
                        Class.forName("com.mysql.jdbc.Driver");
                        conn = DriverManager.getConnection(DB_URL, USER, PASS);
                        stmt = conn.createStatement();
                        String sql = "INSERT INTO Playedmaps(mapid, score_1, score_2) VALUES( ?, ?, ?)";
                        PreparedStatement prest;
                        prest = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
                        prest.setInt(1, mapid[i]);
                        prest.setInt(2, score_1[i]);
                        prest.setInt(3, score_2[i]);
                        prest.executeUpdate();
                        ResultSet rs = prest.getGeneratedKeys();
                        if(rs.next())
                        {
                            pmapid.add(rs.getInt(1));
                        }

                    } catch (SQLException se) {
                        se.printStackTrace();
                    } catch (Exception e) {
                        e.printStackTrace();
                    } finally {
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
                try{
                    Class.forName("com.mysql.jdbc.Driver");
                    conn = DriverManager.getConnection(DB_URL, USER, PASS);
                    stmt = conn.createStatement();
                    PreparedStatement update = conn.prepareStatement
                            ("INSERT INTO Map_belongs_to(mid, pmapid) VALUES(?, ?)");

                    update.setInt(1, mid);
                    update.setInt(2, pmapid.get(i));
                    update.executeUpdate(); // Run query
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
        }
    }




    /*
        USER COMMANDS
     */

    /* Delete match ids, seperated by ",". This is done by deleting a row in
     * the database, then cascade.
     */
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


        String sql = "DELETE FROM Map_belongs_to WHERE mid = ";
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
            String sql = "DELETE FROM Matches2 WHERE complete = 0 AND (match_date = '"+yest+"' OR match_date = '"+twoz+"'  OR match_date = '"+threez+"') ";
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

    // Delete playertransfers in case of mistakes, seperated by ","
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


    // Marks teams inactive if no matches for 90 days


    public static void addmatches() {

        System.out.println("Enter hltv links");
        Scanner scanner = new Scanner(System.in);
        String line = scanner.nextLine();


        String[] values = line.split(", ");
        for(String s: values) {
            scrapepage("Insert", s);
        }
        System.out.println("Matches has been added");
    }
    public static void moveplayers(){
        System.out.println("Enter teamid 1 (leaving team)");
        Scanner scanner = new Scanner(System.in);
        Integer tid1 = scanner.nextInt();

        System.out.println("Enter teamid 2 (joining team)");
        Scanner scanner2 = new Scanner(System.in);
        Integer tid2 = scanner.nextInt();

        Connection conn = null;
        Statement stmt = null;
        try{
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection(DB_URL, USER, PASS);
            stmt = conn.createStatement();
            String sql = "UPDATE Belongs_to" +
                    "     SET tid = "+tid2+" WHERE tid = "+tid1+"";
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
        markteaminactive(tid1);
        System.out.println("Players has been moved");
    }

    public static void markcomplete(){
        Connection conn = null;
        Statement stmt = null;
        System.out.println("Enter match ids");
        Scanner scanner = new Scanner(System.in);
        String line = scanner.nextLine();
        String[] tokens = line.split(", ");
        Integer[] numbers = new Integer[tokens.length];

        for (int i=0; i<numbers.length;i++){
            numbers[i] = Integer.parseInt(tokens[i]);}


        String sql = "UPDATE Matches2 SET COMPLETE = 1 WHERE mid = ";
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
        System.out.println("matches has been marked complete");
    }



}