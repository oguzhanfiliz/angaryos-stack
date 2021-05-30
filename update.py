import sys
import os
import logging
import traceback
import datetime
    


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

    def copy(source, target, mv = False):
        write_log(1, "Copy " + source + " > " + target)
        
        if source[-1] == "/":
            source = source[0: -1]

        if target[-1] == "/":
            target = target[0: -1]

        targetParent = os.path.dirname(target)

        if os.path.isfile("./"+source):
            os.popen("mkdir -p "+targetParent).read()
        else:
            os.popen("mkdir -p "+target).read()

        cmd = "rsync -a --info=progress2"
        if mv:
            cmd = cmd + " --remove-source-files"
            
        os.system(cmd+" "+source+" "+targetParent)

    def temp_file_operatisons():
        os.popen("rm -rf ./temp").read()
        os.popen("mkdir ./temp").read()

        write_log(1, "Temp file operations OK")

    def save_backup():
        write_log(1, "Save backup files starting")

        now = datetime.datetime.now()
        dateTimeStr = str(now.year)+"_"+str(now.month)+"_"+str(now.day)+"_"+str(now.hour)+"_"+str(now.minute)
        basePath = "./../AngaryosBackup"+dateTimeStr+"/"

        files = [
            "backend/.env", 
            "backend/app/",
            "backend/config/", 
            "backend/routes/", 
            "frontend/src/aero/", 
            "services/geoserver/", 
            "services/postgresql/data/",
            "frontend/src/environments/"
        ]

        for f in files:
            copy("./"+f, basePath+f)

        write_log(1, "Save backup files OK")  

    def save_ignored_files():
        write_log(1, "Save ignored files starting")

        temp_file_operatisons()  
        
        files = [
            "./frontend/src/environments/", 
            "./backend/.env", 
            "./services/postgresql/data/"
        ]

        for f in files:
            copy("./"+f, "./temp/"+f)    

        if os.path.exists(".updateignore"):
            f = open(".updateignore", "r")
            for item in f:
                if len(item.strip()) > 0:
                    copy("./"+item.strip(), "./temp/"+item.strip())       
            f.close()
        
        write_log(1, "Save ignored files OK")

    def clone_ignored_files():  
        write_log(1, "Clone ignored files starting")

        files = [
            "./frontend/src/environments/", 
            "./backend/.env", 
            "./services/postgresql/data/"
        ]

        for f in files:
            copy("./temp/"+f, "./"+f, True) 
        
        if os.path.exists(".updateignore"):
            f = open(".updateignore", "r")
            for item in f:
                if len(item.strip()) > 0:
                    copy("./temp/"+item.strip(), "./"+item.strip(), True)      
            f.close()

        write_log(1, "Clone ignored files OK")

    def dump_db():
        rt = os.popen("docker exec $(docker container ls | grep postgis | awk -F ' ' '{print $1}') /bin/bash -c 'pg_dump -Fc postgres -U postgres -h postgresql -f /var/lib/postgresql/`date +%Y-%m-%d_%H:%M`.dump'").read()
        
        if rt != "":
            print("Error for dump_db: " +rt)
            sys.exit()

    def stop_stack():
        os.popen("docker stack rm angaryos 2> /dev/null").read()

        write_log(1, "Stop stack OK")

    def start_stack():
        write_log(1, "Start stack waiting...")
        os.popen("docker stack deploy --compose-file ./docker-stack.yml angaryos").read()
        write_log(1, "Start stack OK")
    
    def clone_repo():
        write_log(1, "Clone repo starting")
        os.popen("git clone https://github.com/MikroGovernment/angaryos-stack.git").read()
        write_log(1, "Clone repo OK")

        os.popen("cp -rf ./angaryos-stack/ ./../").read()
        write_log(1, "Copy repo OK")

        os.popen("rm -rf ./angaryos-stack/").read()
        write_log(1, "Remove repo OK")

    def remove_temp():
        os.popen("rm -rf ./temp").read()

        write_log(1, "Temp file remove OK")
    
    def set_permission():
        os.popen("chmod 755 -R ./frontend/").read()
        os.popen("chmod 755 -R ./backend/").read()
        os.popen("chmod 777 -R ./backend/storage/").read()
        os.popen("chmod 777 -R ./backend/public/").read()
        os.popen("chmod 777 -R ./backend/bootstrap/cache/").read()
        os.popen("chmod 600 ./services/postgresql/.pgpass").read()

        write_log(1, "Set permission OK")

    def main():
        pre_load()
        dump_db()
        stop_stack()  
        save_backup()
        save_ignored_files()
        clone_repo()     
        clone_ignored_files()
        remove_temp()
        set_permission()
        start_stack() 

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