import sys
import os
import subprocess
import json

def scan_file(file_path, antivirus_path):
    try:
        result = subprocess.run([antivirus_path, '-i', file_path], capture_output=True, text=True, check=True)
        if result.returncode == 1:
            return {"result" : result.stdout, "log" : f"Threat found in {file_path}: {result.stdout}"}
        elif result.returncode == 0:
            return {"result" : "No threats found", "log" : ""}
    except subprocess.CalledProcessError as e:
        return {"result" : "Error occurred during scan OR Detect Virus", "log" : f"Error occurred during : {e} OR Detect Virus"}
    except Exception as e:
        return {"result" : "Error occurred during scan ", "log" : f"Error occurred during scan : Exception: {e}, Args:{sys.argv}"}
    
def main():
    
    if len(sys.argv) < 3:
        print(f"Usage: python scan_file.py <file_path> <antivirus_path>")
        sys.exit(1)

    file_path = sys.argv[1]
    antivirus_path = sys.argv[2]

    try:
        scan_result = scan_file(file_path, antivirus_path)
        if scan_result['result'] != "No threats found":
            if os.path.exists(file_path):
                os.remove(file_path)
        print(json.dumps(scan_result))
    except Exception as e:
        print(json.dumps({"result" : "Error occurred during scan ", "log" : f"Error occurred during : Exception: {e}, Args:{sys.argv}"}))


if __name__ == "__main__":
    main()