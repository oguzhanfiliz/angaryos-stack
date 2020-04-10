import sys
import os
import logging
import traceback
    


try:
    
    #Log Vars
    log_file = "update.log"
    
    logger = logging.getLogger(__name__)
    logger.setLevel(logging.INFO)

    handler = logging.FileHandler(log_file)
    handler.setLevel(logging.INFO)

    formatter = logging.Formatter("%(asctime)s - %(levelname)s - %(message)s")
    handler.setFormatter(formatter)

    logger.addHandler(handler)
    
    
    
    #General Vars
    debug = True
    error_trace = True
    started = False

    
    
    #Objects
    


    #Fonctions    
    def write_log(l, s):#l => 1: info, 2:warning, 3:error
        global debug
        
        logger.info('%s', str(s))
        
        if debug:
            print("LOG -> level:" + str(l) + " - " + str(s))

    def pre_load():
        global started 
        
        with open(log_file, "a") as f:
            f.write("\n")

        write_log(1, "Started")

        started = True  

    def copy_to_temp(path):
        write_log(1, "Copy to temp: " + path)

        os.popen("mkdir -p ./temp/"+path)

        if os.path.isfile("./"+path):
            os.popen("rm -rf ./temp/"+path)
            os.popen("cp ./"+path+" ./temp/"+path)
        else:
            os.popen("cp -rf ./"+path+" ./temp/"+path+"/../")

    def clone_from_temp(path):
        write_log(1, "Clone from temp: " + path)

        if os.path.isfile("./"+path):
            os.popen("cp ./temp/"+path+" ./"+path)
        else:
            os.popen("cp -rf ./temp/"+path+"/ ./"+path+"/../")

        

    def temp_file_operatisons():
        os.popen("rm -rf ./temp")
        os.popen("mkdir ./temp")

        write_log(1, "Temp file operations OK")

    def save_ignored_files():
        write_log(1, "Save ignored files starting")

        temp_file_operatisons()
        
        f = open(".updateignore", "r")
        for item in f:
            copy_to_temp(item.strip())        
        f.close()
        
        write_log(1, "Save ignored files OK")

    def clone_ignored_files():  
        write_log(1, "Clone ignored files from temp starting")

        f = open(".updateignore", "r")
        for item in f:
            clone_from_temp(item.strip())        
        f.close()

        write_log(1, "Clone ignored files from temp OK")

    def stop_stack():
        os.popen("docker stack rm angaryos 2> /dev/null")

        write_log(1, "Stop stack OK")
    
    def clone_repo():
        write_log(1, "Clone repo starting")

        os.popen("git clone https://github.com/karapazar/Angaryos").read()
        os.popen("cp -rf ./Angaryos/ ./../").read()
        os.popen("rm -rf ./Angaryos/").read()

        write_log(1, "Clone repo OK")

    def remove_temp():
        os.popen("rm -rf ./temp/").read()

    def main():
        pre_load()
        save_ignored_files()
        stop_stack()   
        clone_repo()     
        clone_ignored_files()
        remove_temp()

    #Main
    if __name__ == "__main__":
        main()


    
except SystemExit as e:
    sys.exit(e)
    
except Exception as e:
    try:
        write_log(3, "Error handled")
    except Exception as ee:
        def write_log(l, s):
            m = "Level: " + str(l) + " - " + str(s)
            print(m)
            with open(log_file, "a") as f:
                f.write(m)
                
        write_log(3, "Error handled")
    
    exc_type, exc_obj, exc_tb = sys.exc_info()    
    write_log(3, "General error! " + str(e) + " (line: " + str(exc_tb.tb_lineno) + ")")
    
    if error_trace:
        print(traceback.print_exc())