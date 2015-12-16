package csgoscraper;

import java.util.Scanner;
import java.util.concurrent.Callable;

import static csgoscraper.Database.*;

/**
 * Created by Brinderp on 12-11-2015.
 */
class Task implements Callable<String> {
    @Override
    public String call() throws Exception {
        String[] commands = new String[5];
        commands[0] = "delete .......... Deletes match by id";
        commands[1] = "delall .......... Delete all inactive matches";
        commands[2] = "deltransfer ..... Deletes transfer by id";
        commands[3] = "kappa  .......... Kappa";
        commands[4] = "markinactive  ... Marks teams inactive, if no matches in 90 days";
        Scanner scanner = new Scanner(System.in);
        String command = scanner.nextLine();
        command = command.toLowerCase();
        switch (command) {
            case "commands":
                for(int i = 0; i < commands.length; i++){
                    System.out.println(commands[i]);
                }
                System.out.println("\n");
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
                for(int i = 0; i < commands.length; i++){
                    System.out.println(commands[i]);
                }
                System.out.println("\n");
                break;
            case "kappa":
                System.out.println("KAPPA KAPPA KAPPA KAPPA KAPPA KAPPA");
                break;
            case "markinactive":
                markinactive();
                break;
            default:
                System.out.println("No such command"); break;
        }
        return "Exiting commands";
    }
}