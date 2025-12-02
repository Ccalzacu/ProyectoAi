import socket
import importlib
import sys
import traceback
import os

HOST = '0.0.0.0'
PORT = 4450

print("Starting Python server...")

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind((HOST, PORT))
server_socket.listen(5)
print(f"Server listening on port {PORT}")

try:
    while True:
        client_socket, addr = server_socket.accept()
        print(f"New incoming connection from {addr}")

        try:
            # Receive data
            data = client_socket.recv(1024).decode().strip()
            if not data:
                continue

            # Expecting two lines: path and function call
            lines = data.splitlines()
            if len(lines) < 2:
                client_socket.sendall(b"Error: Expected two lines (path and function_call)\0")
                continue

            module_path = lines[0].strip()
            func_call = lines[1].strip()

            print(f"User path: {module_path}")
            print(f"Function call: {func_call}")

            # Add path to sys.path if not already there
            if module_path not in sys.path:
                sys.path.insert(0, module_path)
                print(f"Added '{module_path}' to sys.path")

            # Extract module name (the .py file name without extension)
            func_name = func_call.split('(')[0]
            module_name = os.path.splitext(os.path.basename(module_path))[0]

            # Import dynamically
            try:
                # If the path is actually a directory, we assume a package/module inside it
                if os.path.isdir(module_path):
                    module_name = func_name.split('.')[0] if '.' in func_name else func_name
                    print(f"Importing module '{module_name}' from directory '{module_path}'")
                    module = importlib.import_module(module_name)
                else:
                    # If it's a file, add its directory to sys.path and import by name
                    directory = os.path.dirname(module_path)
                    if directory not in sys.path:
                        sys.path.insert(0, directory)
                    module = importlib.import_module(module_name)
                    print(f"Imported module '{module_name}' from file '{module_path}'")
            except Exception as e:
                msg = f"Error importing module: {e}\0"
                client_socket.sendall(msg.encode())
                traceback.print_exc()
                continue

            # Evaluate the function call in that module’s namespace
            try:
                func_sent = func_name.split('.')[1] if '.' in func_name else func_name
                ##result = eval(f"{func_call}")
                print(f"Calling function: {func_sent} from module: {module_name}")

                f=getattr(module, func_sent)
                # 3. Execute the function call string
                exec_globals = {func_sent: f}
                func_args = func_call.split('.')[1] if '.' in func_call else func_call
                exec_string = f"result = {func_args}"
                print(f"Executing: {exec_string}")
                exec_locals = {}
                exec(exec_string, exec_globals, exec_locals)
                result = exec_locals.get('result', None)

                #result = f()
                status = str(result)
            except Exception as e:
                status = f"Function error: {e}"
                traceback.print_exc()

            # Send back the result
            client_socket.sendall((status + '\0').encode())
            client_socket.shutdown(socket.SHUT_WR)

        except Exception as e:
            print(f"Exception handling client {addr}: {e}")
            traceback.print_exc()

        print("Closing connection with client.")
        client_socket.close()

except KeyboardInterrupt:
    print("\nServer stopped by user.")

except Exception as e:
    print("Server exception:", e)
    traceback.print_exc()

finally:
    server_socket.close()
    print("Server socket closed.")

