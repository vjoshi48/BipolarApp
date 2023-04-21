import sys
import json
import torch
import torch.nn as nn

import numpy as np
from pdb import set_trace

# Load the data from the command-line argument
# Load the data from a JSON file
with open('data.json') as f:
    data_string = json.load(f)

data = []

for i in data_string:
    data.append(float(i['Symptoms']))
    
data = torch.tensor(data)

class Prodromal(nn.Module):
    def __init__(self, num_features, hidden_dim, num_classes):
        super(Prodromal, self).__init__()
        self.fc1 = nn.Linear(num_features, hidden_dim)
        self.dropout1 = nn.Dropout(p=0.8)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim) #used to be hidden dim
        self.dropout2 = nn.Dropout(p=0.5)
        self.fc3 = nn.Linear(hidden_dim, hidden_dim)
        self.dropout3 = nn.Dropout(p=0.3)
        self.fc4 = nn.Linear(hidden_dim, num_classes)
        self.relu = nn.ReLU()

        # Xavier initialization for each linear layer
        nn.init.xavier_uniform_(self.fc1.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc2.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc3.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc4.weight, gain=1.0)

    def forward(self, x):
        #set_trace()
        x = self.fc1(x)
        x = self.relu(x)
        x = self.dropout1(x)
        x = self.fc2(x)
        x = self.relu(x)
        x = self.dropout2(x)
        x = self.fc3(x)
        x = self.relu(x)
        x = self.dropout3(x)
        x = self.fc4(x)
        x = torch.softmax(x, dim=1)  # use softmax as the activation function for the final layer
        return x

batch_size = 1
num_features = 14
num_classes = 4

# Define your model and optimizer
model = Prodromal(num_features=num_features, hidden_dim=5, num_classes=num_classes)

# Load the PyTorch model
model = torch.load('best_model1.pt')

# Make your PyTorch prediction
data = data.reshape(1, 14)
prediction = model(data)

# Return the prediction as a JSON string
output = []
l = ['Depressed', 'Manic', 'Hypomanic', 'NoEpisode']

for i in range(len(l)):
    item = l[i]
    output.append({item : float(prediction[0, i].item())})
    

final = json.dumps(output)
print(final)