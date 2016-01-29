package csgoscraper;

import java.util.Scanner;

import static csgoscraper.Database.*;
import static csgoscraper.Database.markcomplete;
import static csgoscraper.Database.moveplayers;

/**
 * @author Martin "Shrewbi" Thiele
 * @since  29-01-2016
 */
public class Commandline {
    public static void main(String[] args) {
        String[] commands = new String[6];
        commands[0] = "delete............Deletes match by id";
        commands[1] = "delall............Delete all inactive matches";
        commands[2] = "deltransfer.......Deletes transfer by id";
        commands[3] = "addmatches........Add matches to database taking hltv links";
        commands[4] = "moveplayers.......Move players from a team to another";
        commands[5] = "markcomplete......Mark matches complete";
        while (true) {
            System.out.print("Enter command: ");
            Scanner scanner = new Scanner(System.in);
            String command = scanner.next();
            command = command.toLowerCase();

            switch (command) {
                case "commands":
                    for (int i = 0; i < commands.length; i++) {
                        System.out.println(commands[i]);
                    }
                    break;
                case "delete":
                    delete();
                    break;
                case "delall":
                    deleteinactive();
                    break;
                case "deltransfer":
                    deletetransfer();
                    break;
                case "help":
                    for (int i = 0; i < commands.length; i++) {
                        System.out.println(commands[i]);
                    }
                    break;
                case "addmatches":
                    addmatches();
                    break;
                case "moveplayers":
                    moveplayers();
                    break;
                case "markcomplete":
                    markcomplete();
                    break;
                default:
                    System.out.println("No such command");
                    break;
            }
        }
    }
}
