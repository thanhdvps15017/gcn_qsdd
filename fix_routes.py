import os
import re

base_dir = "resources/views"
prefixes = ['roles', 'users', 'loai-ho-so', 'loai-thu-tuc', 'xa', 'mau-word']

for root, _, files in os.walk(base_dir):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r') as f:
                content = f.read()
            
            new_content = content
            for prefix in prefixes:
                pattern = r"route\('" + prefix + r"\."
                replacement = "route('settings." + prefix + "."
                new_content = re.sub(pattern, replacement, new_content)
                
            if new_content != content:
                with open(filepath, 'w') as f:
                    f.write(new_content)
                print(f"Updated {filepath}")
